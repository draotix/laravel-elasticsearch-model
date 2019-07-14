<?php

namespace Draotix\LaravelElasticsearchModels;

use Draotix\LaravelElasticsearchModels\Traits\Searchable;

class Elastic
{
    use Searchable;

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table): void
    {
        $this->table = $table;
    }
}