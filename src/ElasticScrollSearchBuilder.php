<?php

namespace Draotix\LaravelElasticsearchModels;

use Draotix\LaravelElasticsearchModels\Helpers\Response;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class ElasticScrollSearchBuilder
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var string
     */
    private $index;

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $scroll_id;

    /**
     * @var string
     */
    private $scroll_time = "1m";

    /**
     * @var array
     */
    private $query;

    /**
     * @var bool
     */
    private $has_data = true;

    /**
     * @var Response
     */
    private $response = [];

    public function __construct(Model $model, int $scroll_id = 0)
    {
        $this->model = $model;

        $this->index = $this->model->getTable();

        if ($scroll_id) {
            $this->scroll_id = $scroll_id;
        }

        $this->client = ClientBuilder::create()
            ->build();
    }

    /**
     * Returns false if no more data available
     *
     * @return bool
     */
    public function hasData()
    {
        return $this->has_data;
    }

    /**
     * Create query by using QueryBuilder class
     *
     * @param callable $callback
     * @param int $size
     * @return $this\
     */
    public function query(callable $callback, int $size = 0)
    {
        $query_builder = $callback(new QueryBuilder());

        if ($query_builder instanceof QueryBuilder) {
            $this->query = $query_builder->query();
        } else {
            throw new InvalidArgumentException("Callback must return instance of QueryBuilder");
        }

        if ($size) {
            $this->size = $size;
        }
        return $this;
    }

    /**
     * Handle next request
     *
     * @return bool
     */
    public function next()
    {
        if ($this->scroll_id) {
            $params = $this->nextBatch();

            $response = $this->client->scroll($params);
        } else {
            $params = $this->build();

            $response = $this->client->search($params);

            $this->scroll_id = $response['_scroll_id'];
        }

        $this->response = Response::handleResponse($this->model, $response);

        $this->checkHitsResult();

        return $this->has_data;
    }

    /**
     * If there are no results make $has_data to false to tell that there are no more data available
     */
    private function checkHitsResult()
    {
        if (! $this->response->hasData()) {
            $this->has_data = false;

            $this->clear();
        }
    }

    /**
     * Clear scroll
     */
    public function clear()
    {
        $params = [
            "scroll_id" => $this->scroll_id
        ];

        $this->client->clearScroll($params);
    }

    /**
     * Get result of search
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get next batch result
     *
     * @return array
     */
    private function nextBatch()
    {
        $params = [
            "body" => [
                "scroll" => $this->scroll_time,
                "scroll_id" => $this->scroll_id
            ]
        ];

        return $params;
    }

    /**
     * Build scroll search for the first time
     *
     * @return array
     */
    private function build()
    {
        $params = [
            "index" => $this->index,
            "type"  => "_doc",
            "scroll"  => $this->scroll_time,
            "body"  => [
                "query" => $this->query
            ]
        ];

        if ($this->size) {
            $params['size'] = $this->size;
        }

        return $params;
    }

}