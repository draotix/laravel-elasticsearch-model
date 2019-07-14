<?php

namespace Draotix\LaravelElasticsearchModels\Traits\QueryBuilders;

use InvalidArgumentException;

trait FullText
{
    protected $multiMatchParams = ["best_fields", "most_fields", "cross_fields", "phrase", "phrase_prefix"];

    protected $simpleQueryStringParams = [
        "default_operator",
        "analyzer",
        "flags",
        "analyze_wildcard",
        "lenient",
        "minimum_should_match",
        "quote_field_suffix",
        "auto_generate_synonyms_phrase_query",
        "all_fields",
        "fuzzy_prefix_length",
        "fuzzy_max_expansions",
        "fuzzy_transpositions",
    ];

    protected $queryStringParams = [
        "default_operator",
        "analyzer",
        "quote_analyzer",
        "allow_leading_wildcard",
        "enable_position_increments",
        "fuzzy_max_expansions",
        "fuzziness",
        "fuzzy_prefix_length",
        "fuzzy_transpositions",
        "phrase_slop",
        "boost",
        "analyze_wildcard",
        "max_determinized_states",
        "minimum_should_match",
        "lenient",
        "time_zone",
        "quote_field_suffix",
        "auto_generate_synonyms_phrase_query",
    ];

    /**
     * Accept text/numeric/dates, analyzes them, and constructs a query
     *
     * @param string $field
     * @param $query
     * @param string $operator
     * @param string $fuzziness
     * @param string $zeroTermsQuery
     * @param float $cutoffFrequency
     * @param bool $autoGenerateSynonymsPhraseQuery
     * @return $this
     */
    public function match(
        string $field,
        $query,
        string $operator = "",
        string $fuzziness = "",
        string $zeroTermsQuery = "",
        float $cutoffFrequency = 0,
        bool $autoGenerateSynonymsPhraseQuery = true
    ) {
        $preparedQuery = [
            $field => [
                "query" => $query
            ]
        ];

        if ($operator) {
            $preparedQuery[$field]["operator"] = $operator;
        }

        if ($fuzziness) {
            $preparedQuery[$field]["fuzziness"] = $fuzziness;
        }

        if ($zeroTermsQuery) {
            $preparedQuery[$field]["zero_terms_query"] = $zeroTermsQuery;
        }

        if ($cutoffFrequency) {
            $preparedQuery[$field]["cutoff_frequency"] = $cutoffFrequency;
        }

        if (! $autoGenerateSynonymsPhraseQuery) {
            $preparedQuery[$field]["auto_generate_synonyms_phrase_query"] = $autoGenerateSynonymsPhraseQuery;
        }

        $this->saveQueryInstance('match', $preparedQuery);

        return $this;
    }

    /**
     * Analyzes the text and creates a phrase query out of the analyzed text.
     *
     * @param string $field
     * @param $query
     * @param string $analyzer
     * @return $this
     */
    public function matchPhrase(string $field, $query, string $analyzer = "")
    {
        $preparedQuery = [
            $field => [
                "query" => $query
            ]
        ];

        if ($analyzer) {
            $preparedQuery[$field]["analyzer"] = $analyzer;
        }

        $this->saveQueryInstance('match_phrase', $preparedQuery);

        return $this;
    }

    /**
     * The match_phrase_prefix is the same as match_phrase, except that it allows for prefix matches on
     * the last term in the text.
     *
     * @param string $field
     * @param $query
     * @param int $maxExpansions
     * @return $this
     */
    public function matchPhrasePrefix(string $field, $query, int $maxExpansions = 50)
    {
        $preparedQuery = [
            $field => [
                "query" => $query
            ]
        ];

        if ($maxExpansions != 50) {
            $preparedQuery[$field]["max_expansions"] = $maxExpansions;
        }

        $this->saveQueryInstance('match_phrase_prefix', $preparedQuery);

        return $this;
    }

    /**
     * The multi_match query builds on the match query to allow multi-field queries.
     *
     * @param $query
     * @param array $fields
     * @param array $params
     * @return $this
     */
    public function multiMatch($query, array $fields, array $params = [])
    {
        $query = [
            "query" => $query,
            "fields" => $fields
        ];

        $query = $this->fillParameters($query, $params, "multi_match_params");

        $this->saveQueryInstance('multi_match', $query);

        return $this;
    }

    /**
     * The common terms query divides the query terms into two groups: more important and less important.
     * First it searches for documents which match the more important terms. These are the terms which appear in
     * fewer documents and have a greater impact on relevance.
     *
     * @param string $field
     * @param $query
     * @param float $cutoffFrequency
     * @param string $lowFreqOperator
     * @param int $minimumShouldMatch
     * @return $this
     */
    public function common(
        string $field,
        $query,
        float $cutoffFrequency = 0.001,
        string $lowFreqOperator = "",
        int $minimumShouldMatch = 0
    ) {
        $preparedQuery = [
            $field => [
                "query" => $query,
                "cutoff_frequency" => $cutoffFrequency
            ]
        ];

        if ($lowFreqOperator) {
            $preparedQuery[$field]["low_freq_operator"] = $lowFreqOperator;
        }

        if ($minimumShouldMatch) {
            $preparedQuery[$field]["minimum_should_match"] = $minimumShouldMatch;
        }

        $this->saveQueryInstance('common', $preparedQuery);

        return $this;
    }

    /**
     * A query that uses a query parser in order to parse its content
     *
     * @param string $query
     * @param string $defaultField
     * @param array $fields
     * @param array $params
     * @return $this
     */
    public function queryString(string $query, $defaultField = "", array $fields = [], array $params = [])
    {
        $query = [
            "query" => $query
        ];

        if ($defaultField) {
            $query["default_field"] = $defaultField;
        }

        if ($fields) {
            $query["fields"] = $fields;
        }

        $query = $this->fillParameters($query, $params, "query_string_params");

        $this->saveQueryInstance('query_string', $query);

        return $this;
    }

    /**
     * A query that uses the SimpleQueryParser to parse its context.
     *
     * @param string $query
     * @param array $fields
     * @param array $params
     * @return $this
     */
    public function simpleQueryString(string $query, array $fields = [], array $params = [])
    {
        $query = [
            "query" => $query
        ];

        if ($fields) {
            $query["fields"] = $fields;
        }

        $query = $this->fillParameters($query, $params, "simple_query_string_params");

        $this->saveQueryInstance('simple_query_string', $query);

        return $this;
    }

    /**
     * Sets extra parameters for a query type
     *
     * @param array $query
     * @param array $params
     * @param string $key
     * @return array
     */
    private function fillParameters(array $query, array $params, string $key)
    {
        $paramsNames = array_keys($params);
        foreach ($paramsNames as $paramName) {

            if (! in_array($paramName, $this->$key)) {
                new InvalidArgumentException("Wrong parameters for {$key} query.");
            }

            $query = array_merge($query, $params);
        }

        return $query;
    }
}