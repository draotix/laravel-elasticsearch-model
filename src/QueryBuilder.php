<?php

namespace Draotix\LaravelElasticsearchModels;

use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\CompoundQueries;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\FullText;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\Geo;
use Draotix\LaravelElasticsearchModels\Traits\QueryBuilders\TermLevel;

class QueryBuilder
{
    use Geo,
        FullText,
        TermLevel,
        CompoundQueries;

    private $query = [];

    /**
     * @var bool Allowing to create multiple search with same type
     */
    protected $allowMultiple = false;

    public function query()
    {
        return $this->query;
    }

    /**
     * Change allow multiple.
     * Allowing to create multiple search with same type
     * Example :
     * $query = [
     *  [ "term" => ...],
     *  [ "term" => ...]
     * ]
     */
    public function allowMultiple()
    {
        $this->allowMultiple = ! $this->allowMultiple;
    }

    /**
     * Get all results.
     */
    public function all()
    {
        $this->query = [
            "match_all" => (object)[],
        ];
    }

    /**
     * The parent_id query can be used to find child documents which belong to a particular parent.
     *
     * @param string $type
     * @param string $id
     * @param bool $ignore_unmapped
     * @return $this
     */
    public function parentId(string $type, string $id, $ignore_unmapped = false)
    {
        $this->query["parent_id"] = [
            "type" => $type,
            "id" => $id
        ];

        if ($ignore_unmapped) {
            $this->query["parent_id"]['ignore_unmapped'] = $ignore_unmapped;
        }

        return $this;
    }

    /**
     * Create array of the search query of the same type
     *
     * @param string $type
     * @param $query
     */
    protected function saveQueryInstance(string $type, $query)
    {
        if ($this->allowMultiple) {
            if (! empty($this->query[$type])) {
                $firstQuery = $this->query[$type];
                unset($this->query[$type]);
                $this->query[][$type] = $firstQuery;
                $this->query[][$type] = $query;

            } elseif(
                ! empty($this->query) &&
                is_array($this->query) &&
                ! empty($this->query[0][$type])
            ) {
                $this->query[][$type] = $query;
            } else {
                $this->query[$type] = $query;
            }
        } else {
            $this->query[$type] = $query;
        }
    }
}