<?php

namespace Draotix\LaravelElasticsearchModels\Traits;

use Draotix\LaravelElasticsearchModels\ElasticScrollSearchBuilder;
use Draotix\LaravelElasticsearchModels\ElasticSearchBuilder;
use Illuminate\Database\Eloquent\Model;

trait Search
{
    protected $_type;
    protected $_score;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->_score;
    }

    /**
     * @param mixed $type
     */
    public function _setType($type): void
    {
        $this->_type = $type;
    }

    /**
     * @param mixed $score
     */
    public function _setScore($score): void
    {
        $this->_score = $score;
    }

    public static function search()
    {
        $model = new static();

        if ($model instanceof Model) {
            return new ElasticSearchBuilder($model);
        }

        return null;
    }

    public static function scrollSearch(int $scroll_id = 0)
    {
        $model = new static();

        if ($model instanceof Model) {
            return new ElasticScrollSearchBuilder($model, $scroll_id);
        }

        return null;
    }
}