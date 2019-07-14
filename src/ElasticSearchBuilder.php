<?php

namespace Draotix\LaravelElasticsearchModels;

use Draotix\LaravelElasticsearchModels\Helpers\ElasticsearchHighlight;
use Draotix\LaravelElasticsearchModels\Helpers\Response;
use Draotix\LaravelElasticsearchModels\Helpers\ScriptField;
use Draotix\LaravelElasticsearchModels\Helpers\SearchValidation;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\CompoundQueries;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\FullText;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\Geo;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\TermLevel;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ElasticSearchBuilder
{
    use Geo,
        FullText,
        TermLevel,
        CompoundQueries;

    /**
     * @var Model the elequent
     */
    private $model;

    /**
     * @var string name of the document (usually table name)
     */
    private $index;

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var array the body od the elasticsearch query
     */
    private $body = [];

    /**
     * @var string index type
     */
    private $type;

    /**
     * @var array
     */
    private $aggregations = [];

    /**
     * @var array the query within the elasticsearch body
     */
    private $query = [];

    /**
     * @var array
     */
    private $rescore = [];

    /**
     * @var array
     */
    private $scriptFields = [];

    /**
     * @var
     */
    private $from;

    /**
     * @var
     */
    private $size;

    /**
     * @var string
     */
    private $preference;

    /**
     * @var bool Allowing to create multiple search with same type
     */
    protected $allowMultiple = false;

    private $sortModeType = ["min", "max", "sum", "avg", "median"];

    public function __construct(Model $model, $routing = null)
    {
        $this->model = $model;

        $this->index = $this->model->getTable();

        $this->client = ClientBuilder::create()
            ->build();
    }

    /**
     * @param string $type
     * @param $query
     */
    protected function saveQueryInstance(string $type, $query)
    {
        $this->query[$type] = $query;
    }

    /**
     * Set index type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Enable explain queries.
     *
     * @return $this
     */
    public function enableExplain()
    {
        $this->body['explain'] = true;

        return $this;
    }

    /**
     * Allows to return the doc value representation of a field for each hit.
     *
     * @param array $fields
     * @return $this
     */
    public function docValueFields(array $fields)
    {
        $this->body['doc_value_fields'] = $fields;

        return $this;
    }

    /**
     * @param string $field
     * @param array $inner_hits
     * @return $this
     */
    public function collapse(string $field, array $inner_hits = [])
    {
        $this->body['collapse'] = [
            "field" => $field
        ];

        if ($inner_hits) {
            SearchValidation::validateCollapseFields($inner_hits);
            $this->body['collapse']['inner_hits'] = $inner_hits;
        }

        return $this;
    }

    /**
     * Highlighters enable you to get highlighted snippets from one or more fields in your search results
     * so you can show users where the query matches are.
     *
     * @param $hightlight_data
     * @return $this
     */
    public function highlight($hightlight_data)
    {
        $this->body['highlight'] = $hightlight_data(new ElasticsearchHighlight());

        return $this;
    }

    /**
     * Allows to configure different boost level per index when searching across more than one indices.
     *
     * @param array $params
     * @return $this
     */
    public function indicesBoost(array $params)
    {
        $this->body['indices_boost'] = $params;

        return $this;
    }

    /**
     * Exclude documents which have a _score less than the minimum specified in min_score.
     *
     * @param int $score
     * @return $this
     */
    public function minScore(int $score)
    {
        $this->body['min_score'] = $score;

        return $this;
    }

    /**
     * The post_filter is applied to the search hits at the very end of a search request, after aggregations
     * have already been calculated.
     *
     * @param $callback
     * @return $this
     */
    public function postFilter($callback)
    {
        $this->body['post_filter'] = $callback(new QueryBuilder());

        return $this;
    }

    /**
     * Controls a preference of the shard copies on which to execute the search. By default, Elasticsearch
     * selects from the available shard copies in an unspecified order
     *
     * @param string $preference
     * @return $this
     */
    public function preference(string $preference)
    {
        $this->preference = $preference;

        return $this;
    }

    /**
     * Rescoring can help to improve precision by reordering just the top (eg 100 - 500) documents returned by the
     * query and post_filter phases, using a secondary (usually more costly) algorithm, instead of applying the
     * costly algorithm to all documents in the index.
     *
     * @param callable $callback
     * @param string $score_mode
     * @param int $window_size
     * @param int $query_weight
     * @param int $rescore_query_weight
     * @return $this
     */
    public function rescore(callable $callback,
                            string $score_mode = "",
                            int $window_size = 10,
                            int $query_weight = 1,
                            int $rescore_query_weight = 1
    ) {
        $query_builder = $callback(new QueryBuilder());

        if ($query_builder instanceof QueryBuilder) {
            $query = $query_builder->query();
        } else {
            throw new InvalidArgumentException("Callback must return instance of QueryBuilder");
        }

        if ($score_mode) {
            $score_mode_options = config("el-allowed-parameters.score_mode");
            if (! in_array($score_mode, $score_mode_options)) {
                throw new InvalidArgumentException("Wrong argument for score mode");
            }

            $query['score_mode'] = $score_mode;
        }

        $this->rescore[] = [
            "window_size" => $window_size,
            "query" => $query,
            "query_weight" => $query_weight,
            "rescore_query_weight" => $rescore_query_weight,
        ];

        return $this;
    }

    /**
     * Use the results from the previous page to help the retrieval of the next page.
     *
     * @param mixed ...$args arguments for sort_after
     * @return $this
     */
    public function searchAfter(...$args)
    {
        $this->body['search_after'] = $args;

        return $this;
    }

    /**
     * Returns the sequence number and primary term of the last modification to each search hit.
     *
     * @param bool $term
     * @return $this
     */
    public function seqNoPrimaryTerm(bool $term)
    {
        $this->body['seq_no_primary_term'] = $term;

        return $this;
    }

    /**
     * Allows you to add one or more sorts on specific fields. Each sort can be reversed as well.
     * The sort is defined on a per field level.
     *
     * @param string $fieldName
     * @param string $orderType
     * @param string $mode
     * @param array $nested
     * @param string $missing
     * @param string $unmapped_type
     * @return $this
     */
    public function sort(
        string $fieldName,
        string $orderType,
        string $mode = "",
        array $nested = [],
        string $missing = "",
        string $unmapped_type = ""
    ) {
        $sort = [
            $fieldName => [
                "order" => $orderType
            ]
        ];

        if ($mode) {
            if(! in_array($mode, $this->sortModeType)) {
                new InvalidArgumentException("Invalid argument {$mode} for mode.");
            }

            $sort[$fieldName]["mode"] = $mode;
        }

        if ($nested) {
            $sort[$fieldName]["nested"] = $nested;
        }

        if ($missing) {
            $sort[$fieldName]["missing"] = $missing;
        }

        if ($unmapped_type) {
            $sort[$fieldName]["unmapped_type"] = $unmapped_type;
        }

        $this->body['sort'][] = $sort;

        return $this;
    }

    /**
     * Allow to sort by _geo_distance.
     *
     * @param string $fieldName
     * @param $fieldValue
     * @param string $orderType
     * @param string $mode
     * @param string $unit
     * @param string $distanceType
     * @param bool $ignoreUnmapped
     * @return $this
     */
    public function geoDistanceSort(
        string $fieldName,
        $fieldValue,
        string $orderType,
        string $mode,
        string $unit = "m",
        string $distanceType = "arc",
        bool $ignoreUnmapped = false
    ) {
        $sort = [
            "_geo_distance" => [
                $fieldName => $fieldValue,
                "order" => $orderType,
                "unit" => $unit,
                "distance_type" => $distanceType,
                "ignore_unmapped" => $ignoreUnmapped
            ]
        ];

        if ($mode) {
            if(! in_array($mode, $this->sortModeType)) {
                new InvalidArgumentException("Invalid argument {$mode} for mode.");
            }

            $sort["_geo_distance"]["mode"] = $mode;
        }

        $this->body['sort'][] = $sort;

        return $this;
    }

    /**
     * Allow to sort based on custom scripts.
     *
     * @param string $type
     * @param string $order
     * @param array $script
     * @return $this
     */
    public function scriptSort(string $type, string $order, array $script)
    {
        $sort = [
            "_script" => [
                "type" => $type,
                "script" => $script
            ],
            "order" => $order
        ];

        $this->body['sort'][] = $sort;

        return $this;
    }

    /**
     * Allows to control how the _source field is returned with every hit.
     *
     * @param $source
     * @return $this
     */
    public function source($source)
    {
        $this->body["_source"] = $source;

        return $this;
    }

    /**
     * About fields that are explicitly marked as stored in the mapping, which is off by default and
     * generally not recommended.
     *
     * @param $storeFields
     * @return $this
     */
    public function storeFields($storeFields)
    {
        $this->body["store_fields"] = $storeFields;

        return $this;
    }

    /**
     * Allows you to control how the total number of hits should be tracked.
     *
     * @param $trackTotalHits
     * @return $this
     */
    public function trackTotalHits($trackTotalHits)
    {
        $this->body["track_total_hits"] = $trackTotalHits;

        return $this;
    }

    /**
     * Returns a version for each search hit.
     *
     * @param bool $version
     */
    public function version(bool $version)
    {
        $this->body["version"] = $version;
    }

    /**
     * Allows to return a script evaluation (based on different fields) for each hit.
     *
     * @param string $name
     * @param callable $scriptFieldCallback
     * @return $this
     */
    public function scriptField(string $name, callable $scriptFieldCallback)
    {
        $scriptFieldObj = $scriptFieldCallback(new ScriptField());

        if ($scriptFieldObj instanceof ScriptField) {
            $scriptField = $scriptFieldObj->getField();
        } else {
            throw new InvalidArgumentException("Callback must return instance of QueryBuilder");
        }

        $this->scriptFields[$name] = $scriptField;

        return $this;
    }

    /**
     * Create the query.
     *
     * @return array
     */
    public function query(): array
    {
        $container = [
            "index" => $this->index,
            "type"  => "_doc",
        ];

        if ($this->type) {
            $container["type"] = $this->type;
        }

        if ($this->from) {
            $container["from"] = $this->from;
        }

        if ($this->size) {
            $container["size"] = $this->size;
        }

        if ($this->preference) {
            $container['preference'] = $this->preference;
        }

        if ($this->aggregations) {
            $this->body["aggs"] = $this->aggregations;
        }

        if ($this->scriptFields) {
            $this->body["script_fields"] = $this->scriptFields;
        }

        if ($this->query) {
            $this->body["query"] = $this->query;
        }

        $container['body'] = $this->body;

        if ($this->rescore) {
            $container['rescore'] = $this->rescore;
        }

        return $container;
    }

    /**
     * Set custom parameters to bool queries.
     *
     * @param array $params
     */
    public function boolConfig(array $params = [])
    {
        foreach ($params as $key => $value) {

            if (! isset($this->query['bool'])) {
                $this->query['bool'] = [];
            }

            $this->query['bool'][$key] = $value;
        }
    }

    /**
     * Create bool queries.
     *
     * @param string $type
     * @param callable $queryBuilderCallback
     */
    private function bool(string $type, callable $queryBuilderCallback)
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->allowMultiple();

        $queryBuilder = $queryBuilderCallback($queryBuilder);

        if ($queryBuilder instanceof QueryBuilder) {

            if (! isset($this->query['bool'])) {
                $this->query['bool'] = [];
            }

            if (! isset($this->query['bool'][$type])) {
                $this->query['bool'][$type] = [];
            }

            $query = [];
            if (count($queryBuilder->query()) > 1) {
                foreach ($queryBuilder->query() as $query_data) {
                    $query[] = $query_data;
                }
            } else {
                $query = $queryBuilder->query();
            }

            $this->query['bool'][$type] = $query;
        }
    }

    /**
     * To create a must in bool query.
     *
     * @param callable $queryBuilderCallback
     * @return $this
     */
    public function must(callable $queryBuilderCallback)
    {
        $this->bool("must", $queryBuilderCallback);

        return $this;
    }

    /**
     * To create a must not in bool query.
     *
     * @param callable $queryBuilderCallback
     * @return $this
     */
    public function mustNot(callable $queryBuilderCallback)
    {
        $this->bool("must_not", $queryBuilderCallback);

        return $this;
    }

    /**
     * To create a filter in bool query.
     *
     * @param callable $queryBuilderCallback
     * @return $this
     */
    public function filter(callable $queryBuilderCallback)
    {
        $this->bool("filter", $queryBuilderCallback);

        return $this;
    }

    /**
     * To create a should in bool query.
     *
     * @param callable $queryBuilderCallback
     * @return $this
     */
    public function should(callable $queryBuilderCallback)
    {
        $this->bool("should", $queryBuilderCallback);

        return $this;
    }

    /**
     * A query that generates the union of documents produced by its subqueries, and that
     * scores each document with the maximum score for that document as produced by any subquery,
     * plus a tie breaking increment for any additional matching subqueries.
     *
     * @param callable $queryBuilderCallback
     * @param array $configs
     * @return $this
     */
    public function disMax(callable $queryBuilderCallback, array $configs = [])
    {
        $this->query['dis_max'] = $configs;

        $queryBuilder = $queryBuilderCallback(new QueryBuilder());

        if ($queryBuilder instanceof QueryBuilder) {

            if (! isset($this->query['dis_max']["queries"])) {

                $this->query['dis_max']["queries"] = [];

            }

            $this->query['dis_max']["queries"][] = $queryBuilder->query();

        }

        return $this;
    }

    /**
     * Allows you to modify the score of documents that are retrieved by a query.
     *
     * @param array $params
     * @param callable|null $queryBuilderCallback
     * @param callable|null $scriptFieldBuilder
     * @return $this
     */
    public function functionScore(
        array $params = [],
        callable $queryBuilderCallback = null,
        callable $scriptFieldBuilder = null
    ) {
        $this->query['function_score'] = $params;

        if ($queryBuilderCallback) {
            $queryBuilder = $queryBuilderCallback(new QueryBuilder());

            if ($queryBuilder instanceof QueryBuilder) {
                $this->query['function_score']['query'] = $queryBuilder->query();
            }
        }

        if ($scriptFieldBuilder) {
            $scriptFieldObj = $scriptFieldBuilder(new ScriptField());

            if ($scriptFieldObj instanceof ScriptField) {
                $this->query['function_score']['script_score'] = $scriptFieldObj->getField();
            }
        }

        return $this;
    }

    /**
     * Nested query allows to query nested objects.
     *
     * @param string $path
     * @param string $score_mode
     * @param callable|array $queryBuilder
     * @return $this
     */
    public function nested(string $path, string $score_mode = "avg", $queryBuilder)
    {
        if (is_array($queryBuilder)) {
            $query = $queryBuilder;
        } else {
            $queryBuilder = $queryBuilder(new QueryBuilder());

            if ($queryBuilder instanceof QueryBuilder) {
                $query = $queryBuilder->query();
            } else {
                throw new InvalidArgumentException("Callback should return query builder.");
            }
        }


        $this->query['nested'] = [
            "path" => $path,
            "score_mode" => $score_mode,
            "query" => $query
        ];

        return $this;
    }

    public function aggs(callable $aggregations)
    {
        $aggregations = $aggregations(new AggregationsQueryBuilder());

        if ($aggregations instanceof AggregationsQueryBuilder) {
            $this->aggregations = $aggregations->getQuery();
        }
    }

    /**
     * An intervals query allows fine-grained control over the order and proximity of matching terms.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function intervals(string $name, array $params)
    {
        $this->query['intervals'] = $params;

        return $this;
    }

    /**
     * The has_child filter accepts a query and the child type to run against, and results in parent
     * documents that have child docs matching the query.
     *
     * @param string $type
     * @param callable|null $queryBuilderCallback
     * @param array $params
     * @param string $scoreMode
     * @param int $minChildren
     * @param int $maxChildren
     * @return $this
     */
    public function hasChild(
        string $type,
        callable $queryBuilderCallback = null,
        array $params = [],
        string $scoreMode = "",
        int $minChildren = 0,
        int $maxChildren = 0
    ) {
        if ($queryBuilderCallback) {
            $queryBuilder = $queryBuilderCallback(new QueryBuilder());

            if ($queryBuilder instanceof QueryBuilder) {
                $this->query["has_child"] = [
                    "type" => $type,
                    "query" => $queryBuilder->query()
                ];
            }
        } else {
            $this->query["has_child"] = $params;
        }

        if ($scoreMode) {
            $this->query["has_child"]["score_mode"] = $scoreMode;
        }

        if ($minChildren) {
            $this->query["has_child"]["min_children"] = $minChildren;
        }

        if ($maxChildren) {
            $this->query["has_child"]["max_children"] = $maxChildren;
        }

        return $this;
    }

    /**
     * The query is executed in the parent document space, which is specified by the parent type. This query
     * returns child documents which associated parents have matched.
     *
     * @param string $parentType
     * @param callable|null $queryBuilderCallback
     * @param array $params
     * @param bool $score
     * @return $this
     */
    public function hasParent(
        string $parentType,
        callable $queryBuilderCallback = null,
        array $params = [],
        bool $score = false) {

        if ($queryBuilderCallback) {
            $queryBuilder = $queryBuilderCallback(new QueryBuilder());

            if ($queryBuilder instanceof QueryBuilder) {
                $this->query["has_parent"] = [
                    "parent_type" => $parentType,
                    "query" => $queryBuilder->query()
                ];
            }
        } else {
            $this->query["has_parent"] = $params;
        }

        if ($score) {
            $this->query["has_parent"]["score"] = $score;
        }

        return $this;
    }

    /**
     * Returns documents that contain a value other than null or [] in a provided field.
     *
     * @param string $field
     * @return Response
     */
    public function exists(string $field)
    {
        $this->query['exists'] = [
            "field" => $field
        ];

        return $this->get();
    }

    /**
     * The count API allows to easily execute a query and get the number of matches for that query.
     * It can be executed across one or more indices.
     *
     * @return Response
     */
    public function count()
    {
        $params = $this->query();

        $response = $this->client->count($params);

        return Response::handleResponse($this->model, $response)
            ->getTotal();
    }

    /**
     * Get all results.
     *
     * @param int $limit
     * @return Response
     */
    public function all(int $limit = 10000)
    {
        $this->query = [
            "match_all" => (object)[],
        ];

        return $this->get(0, $limit);
    }

    /**
     * Get results of the query.
     *
     * @param int $from
     * @param int $size
     * @return Response
     */
    public function get(int $from = 0, int $size = 10)
    {
        $this->from = $from;
        $this->size = $size;

        $params = $this->query();

        $response = $this->client->search($params);

        return Response::handleResponse($this->model, $response, $from, $size);
    }
}