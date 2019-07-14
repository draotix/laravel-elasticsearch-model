<?php

namespace Draotix\LaravelElasticsearchModels\Traits\QueryBuilders;

trait CompoundQueries
{
    /**
     * A query that wraps another query and simply returns a constant score equal to the query boost
     * for every document in the filter.
     *
     * @param array $params
     * @return $this
     */
    public function constantScore(array $params = [])
    {
        $this->query['constant_score'] = $params;

        return $this;
    }

    /**
     * Can be used to effectively demote results that match a given query. Unlike the "NOT" clause in bool query,
     * this still selects documents that contain undesirable terms, but reduces their overall score.
     *
     * @param array $positive
     * @param array $negative
     * @param float $negative_boost
     * @return $this
     */
    public function boosting(array $positive = [], array $negative = [], float $negative_boost = 0)
    {
        $this->query['boosting'] = [];

        if ($positive) {
            $this->query['positive'] = $positive;
        }

        if ($negative) {
            $this->query['negative'] = $negative;
        }

        if ($negative_boost) {
            $this->query['negative_boost'] = $negative_boost;
        }

        return $this;
    }
}