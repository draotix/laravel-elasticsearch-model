<?php

namespace Draotix\LaravelElasticsearchModels;

class AnalyzerBuilder
{
    private $analyzer = [];

    public function get()
    {
        return $this->analyzer;
    }

    public function __construct()
    {
        $this->analyzer = [
            'type' => 'custom'
        ];
    }

    public function tokenizer($tokenizerName)
    {
        $this->analyzer['tokenizer'] = $tokenizerName;

        return $this;
    }

    public function charFilter(...$args)
    {
        $this->analyzer['char_filter'] = $args;

        return $this;
    }

    public function filter(...$args)
    {
        $this->analyzer['filter'] = $args;

        return $this;
    }

    public function setPositionIncrementGap(int $value)
    {
        $this->analyzer['position_increment_gap'] = $value;

        return $this;
    }
}