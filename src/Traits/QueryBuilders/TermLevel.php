<?php

namespace Draotix\LaravelElasticsearchModels\Traits\QueryBuilders;

use InvalidArgumentException;

trait TermLevel
{
    private $rangeParams = ["gte", "gt", "lte", "lt", "boost", "time_zone", "format"];

    /**
     * You can use the term query to find documents based on a precise value.
     *
     * @param string $field
     * @param $value
     * @param float $boost
     * @return $this
     */
    public function term(string $field, $value, float $boost = 1.0)
    {
        if ($boost != 1.0) {
            $query = [
                $field =>[
                    "value" => $value,
                    "boost" => $boost
                ]
            ];
        } else {
            $query = [
                $field => $value
            ];
        }

        $this->saveQueryInstance('term', $query);

        return $this;
    }

    /**
     * Filters documents that have fields that match any of the provided terms.
     *
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function multiTerms(string $field, $value = [])
    {
        $query = [
            $field => $value
        ];

        $this->saveQueryInstance('terms', $query);

        return $this;
    }

    /**
     * Terms lookup mechanism.
     *
     * @param string $fieldName
     * @param string $index
     * @param string $id
     * @param string $path
     * @param string $routing
     * @return $this
     */
    public function terms(string $fieldName, string $index, string $id, string $path, string $routing = "")
    {
        $query = [
            $fieldName => [
                "index" => $index,
                "id" => $id,
                "path" => $path
            ]
        ];

        if ($routing) {
            $query['routing'] = $routing;
        }

        $this->saveQueryInstance('terms', $query);

        return $this;
    }

    /**
     * Returns any documents that match with at least one or more of the provided terms.
     *
     * @param string $field
     * @param array $terms
     * @param string $minimumShouldMatchField
     * @return $this
     */
    public function termsSet(string $field, array $terms, $minimumShouldMatchField = "")
    {
        $query = [
            $field => [
                "terms" => $terms
            ]
        ];

        if ($minimumShouldMatchField) {
            $query[$field]['minimum_should_match_field'] = $minimumShouldMatchField;
        }

        $this->saveQueryInstance('terms_set', $query);

        return $this;
    }

    /**
     * Matches documents with fields that have terms within a certain range.
     *
     * @param string $field
     * @param array $params
     * @return $this
     */
    public function range(string $field, array $params)
    {
        $paramNames = array_keys($params);

        foreach ($paramNames as $paramName) {
            if (! in_array($paramName, $this->rangeParams) ) {
                new InvalidArgumentException("Wrong parameter {$paramName} for range.");
            }
        }

        $query = [
            $field => $params
        ];

        $this->saveQueryInstance('range', $query);

        return $this;
    }

    /**
     * Matches documents that have fields containing terms with a specified prefix (not analyzed).
     *
     * @param string $field
     * @param string $value
     * @param float|null $boost
     * @return $this
     */
    public function prefix(string $field, string $value, $boost = null)
    {
        if (! $boost) {
            $query = [
                $field => $value
            ];
        } else {
            $query = [
                $field => [
                    "value" => $value,
                    "boost" => $boost
                ]
            ];
        }

        $this->saveQueryInstance('prefix', $query);

        return $this;
    }

    /**
     * Matches documents that have fields matching a wildcard expression (not analyzed).
     *
     * @param string $field
     * @param string $value
     * @param float|null $boost
     * @return $this
     */
    public function wildcard(string $field, string $value, $boost = null)
    {
        if (! $boost) {
            $query = [
                $field => $value
            ];
        } else {
            $query = [
                $field => [
                    "value" => $value,
                    "boost" => $boost
                ]
            ];
        }

        $this->saveQueryInstance('wildcard', $query);

        return $this;
    }

    /**
     * Allows you to use regular expression term queries.
     *
     * @param string $field
     * @param string $value
     * @param null $boost
     * @param string $flags
     * @param null $maxDeterminizedStates
     * @return $this
     */
    public function regexp(
        string $field,
        string $value,
        $boost = null,
        string $flags = "",
        $maxDeterminizedStates = null
    ) {
        $query = [
            "value" => $value
        ];

        if ($boost) {
            $query["boost"] = $boost;
        }

        if ($flags) {
            $query["flags"] = $flags;
        }

        if ($maxDeterminizedStates) {
            $query['max_determinized_states'] = $maxDeterminizedStates;
        }

        $query = [$field => $query];

        $this->saveQueryInstance('regexp', $query);

        return $this;
    }

    /**
     * Generates matching terms that are within the maximum edit distance specified in fuzziness
     * and then checks the term dictionary to find out which of those generated terms actually exist in the index.
     *
     * @param string $field
     * @param $value
     * @param int $fuzziness
     * @param null $boost
     * @param int $prefixLength
     * @param int $maxExpansions
     * @param bool $transpositions
     * @return $this
     */
    public function fuzzy(
        string $field,
        $value,
        int $fuzziness = 0,
        $boost = null,
        int $prefixLength = 0,
        int $maxExpansions = 50,
        bool $transpositions = true
    ) {
        $query = [
            "value" => $value
        ];

        if ($fuzziness) {
            $query["fuzziness"] = $fuzziness;
        }

        if ($boost) {
            $query['boost'] = $boost;
        }

        if ($prefixLength) {
            $query['prefix_length'] = $prefixLength;
        }

        if ($maxExpansions != 50) {
            $query['max_expansions'] = $maxExpansions;
        }

        if (! $transpositions) {
            $query['transpositions'] = $transpositions;
        }

        $query = [
            $field => $query
        ];

        $this->saveQueryInstance('fuzzy', $query);

        return $this;
    }

    /**
     * Returns documents based on their IDs.
     *
     * @param array $ids
     * @return $this
     */
    public function ids(array $ids) {
        $query = [
            "values" => $ids
        ];

        $this->saveQueryInstance('ids', $query);

        return $this;
    }
}