<?php

namespace Draotix\LaravelElasticsearchModels\Helpers;

class SearchValidation
{
    /**
     * Validate collapse inner_hits parameters
     *
     * @param array $inner_hits
     */
    public static function validateCollapseFields(array $inner_hits)
    {
        $field_options = ["name", "size", "sort"];
        $keys = array_keys($inner_hits);

        foreach ($keys as $key) {
            if (! in_array($key, $field_options)) {
                throw new \InvalidArgumentException("Invalid parameter {$key} for collapse -> inner_hits");
            }
        }
    }
}

