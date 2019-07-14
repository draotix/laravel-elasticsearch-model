<?php

namespace Draotix\LaravelElasticsearchModels;

use Draotix\LaravelElasticsearchModels\Helpers\ConfigAnalysis;
use Draotix\LaravelElasticsearchModels\Helpers\DataTypeParameters;
use Draotix\LaravelElasticsearchModels\Traits\AnalysisConfiguration;
use Draotix\LaravelElasticsearchModels\Traits\MappingFields;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;

class IndexBuilder
{
    use MappingFields,
        AnalysisConfiguration;

    private $client;

    private $model;

    private $index;

    private $dynamic = false;

    private $mapping = [];

    private $field_names = true;

    private $meta = [];

    private $routing = false;

    private $source = true;

    private $analysis = [];

    private $type;

    public function __construct(Model $model = null)
    {
        if ($model && $model instanceof Model) {
            $this->model = $model;
            $this->index = $model->getTable();
        }

        $this->client = ClientBuilder::create()
            ->build();
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
     * @param bool $dynamic
     */
    public function setDynamic(bool $dynamic): void
    {
        $this->dynamic = $dynamic;
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return $this->dynamic;
    }

    /**
     * @param $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function isFieldNameAvailable(string $name) : bool
    {
        if (array_key_exists($name, $this->mapping)) {
            throw new \InvalidArgumentException(
                "Field name: '" . $name . "' is being used. You can't use the same field name twice."
            );
        }
        return true;
    }

    /**
     * Validate field data
     *
     * @param string $type
     * @param string $name
     * @param array $params
     */
    private function validateField(string $type, string $name, array $params) : void
    {
        $this->isFieldNameAvailable($name);

        DataTypeParameters::validateParameters($type, $params);
    }

    /**
     * Callback method for fields that have child fields or properties.
     *
     * @param string $name
     * @param $fields
     * @param string $fields_key
     */
    private function fieldsCallback(string $name, $fields, string $fields_key = "fields")
    {
        if ($fields) {
            $mapBuilder = $fields(new self());
            if ($mapBuilder instanceof IndexBuilder) {
                $this->mapping[$name][$fields_key] = $mapBuilder->getMapping();
            }
        }
    }

    /**
     * Check if index exist
     *
     * @return bool
     */
    private function indexExists()
    {
        $indexParams['index']  = $this->index;
        return $this->client->indices()
            ->exists($indexParams);
    }

    /**
     * Disable field names for the index
     *
     * @return $this
     */
    public function disableFieldNames()
    {
        $this->field_names = false;

        return $this;
    }

    /**
     * Making a routing value required
     *
     * @return $this
     */
    public function requireRouting()
    {
        $this->routing = true;

        return $this;
    }

    /**
     * Disabling the _source field
     *
     * @return $this
     */
    public function disableSource()
    {
        $this->source = false;

        return $this;
    }

    /**
     * Add custom meta data associated with the mapping
     *
     * @param string $class
     * @param string $version_min
     * @param string $version_max
     * @return $this
     */
    public function setMeta(string $class = "", string $version_min = "", string $version_max = "")
    {
        $this->meta = [];

        if ($class) {
            $this->meta['class'] = $class;
        }

        if ($version_min || $version_max) {
            $this->meta['version'] = [];

            if ($version_min) {
                $this->meta['version']['min'] = $version_min;
            }

            if ($version_max) {
                $this->meta['version']['max'] = $version_max;
            }
        }

        return $this;
    }

    /**
     * Set analyzer to the current field
     *
     * @param string $analyzer
     * @return $this
     */
    public function analyzer(string $analyzer)
    {
        // if a custom analyzer with that name doesn't exit check the default analyzers
        if (! array_key_exists($analyzer, $this->analysis['analyzer'])) {
            ConfigAnalysis::analyzisExists($analyzer);
        }

        $lastFieldInsertedKey = key($this->mapping);
        $this->mapping[$lastFieldInsertedKey]['analyzer'] = $analyzer;

        return $this;
    }

    /**
     * Returns elastic query
     *
     * @return array
     */
    public function query(): array
    {
        if ($this->type) {
            $mappings = [
                "dynamic" => $this->dynamic,
                "properties" => $this->mapping
            ];
        } else {
            $mappings = [
                "_doc" => [
                    "dynamic" => $this->dynamic,
                    "properties" => $this->mapping
                ]
            ];
        }

        if ($this->analysis) {
            $mappings["body"]["settings"]["analysis"] = $this->analysis;
        }

        if ($this->meta) {
            $mappings["mappings"]["_doc"]["_meta"] = $this->meta;
        }

        if (! $this->field_names) {
            $mappings["mappings"]["_doc"]["_field_names"] = $this->field_names;
        }

        if ($this->routing) {
            $mappings["mappings"]["_doc"]["_routing"] = $this->routing;
        }

        if (! $this->source) {
            $mappings["mappings"]["_doc"]["_source"] = $this->source;
        }

        $query = [
            "index" => $this->index,
            "body" => [
                "mappings" => $mappings
            ]
        ];

        return $query;
    }

    /**
     * Execute the elastic-search query
     *
     * @return bool
     * @throws \Exception
     */
    public function build()
    {
        if ($this->indexExists()) {
            throw new \ErrorException("Index already exist.");
        }

        try {
            $result = $this->client
                ->indices()
                ->create($this->query());

            if (is_array($result) && isset($result['acknowledged'])) {
                return true;
            }
        } catch (\Exception $exception) {
            $exception = json_decode($exception->getMessage());

            if (isset($exception->error) && isset($exception->error->reason)) {
                throw new \InvalidArgumentException($exception->error->reason);
            }
        }

        return false;
    }

    /**
     * Delete mapping of the model
     *
     * @return array|bool
     */
    public function delete()
    {
        if ($this->indexExists()) {
            $indexParams['index']  = $this->index;

            return $this->client
                ->indices()
                ->delete($indexParams);
        }

        return false;
    }
}
