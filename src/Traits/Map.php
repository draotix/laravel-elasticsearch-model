<?php

namespace Draotix\LaravelElasticsearchModels\Traits;

use Draotix\LaravelElasticsearchModels\IndexBuilder;

trait Map
{
    public static function map()
    {
        $model = new static();
        return new IndexBuilder($model);
    }
}