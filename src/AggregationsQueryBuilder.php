<?php

namespace Draotix\LaravelElasticsearchModels;

use InvalidArgumentException;
use Draotix\LaravelElasticsearchModels\Helpers\ScriptField;

class AggregationsQueryBuilder
{
    private $query = [];

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * General method to handle script field callbacks.
     *
     * @param callable $scriptFieldCallback
     * @return array
     */
    private function scriptField(callable $scriptFieldCallback)
    {
        $scriptFieldObj = $scriptFieldCallback(new ScriptField());

        if ($scriptFieldObj instanceof ScriptField) {
            $scriptField = $scriptFieldObj->getField();
        } else {
            throw new InvalidArgumentException("Callback must return instance of QueryBuilder");
        }

        return $scriptField;
    }

    /**
     * Metrics aggregation that computes the average of numeric values that are extracted from the
     * aggregated documents.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function avg(string $name, string $field, $missing = null, callable $scriptFieldCallback = null)
    {
        $this->query[$name] = [
            "avg" => [
                "field" => $field
            ]
        ];

        if ($missing) {
            $this->query[$name]['avg']['missing'] = $missing;
        }

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]['avg']['script'] = $scriptField;
        }

        return $this;
    }

    /**
     * A single-value metrics aggregation that computes the weighted average of numeric values that are
     * extracted from the aggregated documents. These values can be extracted either from specific numeric
     * fields in the documents.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $valueField
     * @param string $weightField
     * @param bool $isScript
     * @param int|null $valueMissing
     * @param int|null $weightMissing
     * @return $this
     */
    public function weightedAvg(
        string $name,
        string $valueField,
        string $weightField,
        bool $isScript = false,
        int $valueMissing = null,
        int $weightMissing = null
    )
    {
        $type = "field";
        if ($isScript) {
            $type = "script";
        }

        $this->query[$name] = [
            $name => [
                "weighted_avg" => [
                    "value" => [
                        $type => $valueField
                    ],
                    "weight" => [
                        $type => $weightField
                    ]
                ]
            ]
        ];

        if ($valueMissing) {
            $this->query[$name][$name]["weighted_avg"]["value"]["missing"] = $valueMissing;
        }

        if ($weightMissing) {
            $this->query[$name][$name]["weighted_avg"]["weight"]["missing"] = $valueMissing;
        }

        return $this;
    }

    /**
     * A single-value metrics aggregation that calculates an approximate count of distinct values.
     * Values can be extracted either from specific fields in the document or generated by a script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param int|null $precisionThreshold
     * @param int|null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function cardinality(
        string $name,
        string $field,
        int $precisionThreshold = null,
        int $missing = null,
        callable $scriptFieldCallback = null
    )
    {

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name] = [
                "cardinality" => [
                    "script" => $scriptField,
                ]
            ];

        } else {
            $this->query[$name] = [
                "cardinality" => [
                    "field" => $field,
                ]
            ];
        }

        if ($precisionThreshold) {
            $this->query[$name]["cardinality"]["precision_threshold"] = $precisionThreshold;
        }

        if ($missing) {
            $this->query[$name]["cardinality"]["missing"] = $missing;
        }

        return $this;
    }

    /**
     * A multi-value metrics aggregation that computes stats over numeric values extracted
     * from the aggregated documents. These values can be extracted either from specific numeric fields
     * in the documents, or be generated by a provided script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param int|null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function extendedStats(
        string $name,
        string $field,
        int $missing = null,
        callable $scriptFieldCallback = null
    )
    {
        $this->query[$name] = [
            "extended_stats" => [
                "field" => $field,
            ]
        ];

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["extended_stats"]["script"] = $scriptField;
        }

        if ($missing) {
            $this->query[$name]["extended_stats"]["missing"] = $missing;
        }

        return $this;
    }

    /**
     * A metric aggregation that computes the bounding box containing all geo_point values for a field.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param bool $wrapLongitude
     * @return $this
     */
    public function geoBounds(
        string $name,
        string $field,
        bool $wrapLongitude
    )
    {
        $this->query[$name] = [
            "geo_bounds" => [
                "field" => $field,
            ]
        ];

        if ($wrapLongitude) {
            $this->query[$name]["geo_bounds"]["wrap_longitude"] = $wrapLongitude;
        }

        return $this;
    }

    /**
     * A metric aggregation that computes the weighted centroid from all coordinate values for a
     * Geo-point datatype field.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @return $this
     */
    public function geoCentroid(string $name, string $field)
    {
        $this->query[$name] = [
            "geo_centroid" => [
                "field" => $field,
            ]
        ];

        return $this;
    }

    /**
     * A single-value metrics aggregation that keeps track and returns the maximum value among the numeric values
     * extracted from the aggregated documents.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param int|null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function max(
        string $name,
        string $field,
        int $missing = null,
        callable $scriptFieldCallback = null
    )
    {
        $this->query[$name] = [
            "max" => [
                "field" => $field,
            ]
        ];

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["max"]["script"] = $scriptField;
        }

        if ($missing) {
            $this->query[$name]["max"]["missing"] = $missing;
        }

        return $this;
    }

    /**
     * A single-value metrics aggregation that keeps track and returns the minimum value among numeric values
     * extracted from the aggregated documents.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param int|null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function min(
        string $name,
        string $field,
        int $missing = null,
        callable $scriptFieldCallback = null
    )
    {
        $this->query[$name] = [
            "min" => [
                "field" => $field,
            ]
        ];

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["min"]["script"] = $scriptField;
        }

        if ($missing) {
            $this->query[$name]["min"]["missing"] = $missing;
        }

        return $this;
    }

    /**
     * A multi-value metrics aggregation that calculates one or more percentiles over numeric values extracted
     * from the aggregated documents. These values can be extracted either from specific numeric fields in the
     * documents, or be generated by a provided script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param array $percents
     * @param bool|null $keyed
     * @param int|null $missing
     * @param array $tdigest
     * @param array $hdr
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function percentiles(
        string $name,
        string $field,
        array $percents = [],
        bool $keyed = null,
        int $missing = null,
        array $tdigest = [],
        array $hdr = [],
        callable $scriptFieldCallback = null
    )
    {
        $this->query[$name] = [
            "percentiles" => [
                "field" => $field,
            ]
        ];

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["percentiles"]["script"] = $scriptField;
        }

        if ($percents) {
            $this->query[$name]["percentiles"]["percents"] = $percents;
        }

        if ($keyed) {
            $this->query[$name]["percentiles"]["keyed"] = $keyed;
        }

        if ($tdigest) {
            $this->query[$name]["percentiles"]["tdigest"] = $tdigest;
        }

        if ($hdr) {
            $this->query[$name]["percentiles"]["hdr"] = $hdr;
        }

        if ($missing) {
            $this->query[$name]["percentiles"]["missing"] = $missing;
        }

        return $this;
    }

    /**
     * A multi-value metrics aggregation that calculates one or more percentile ranks over numeric values extracted
     * from the aggregated documents. These values can be extracted either from specific numeric fields in the
     * documents, or be generated by a provided script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param array $values
     * @param array $hdr
     * @param int|null $missing
     * @param callable|null $scriptFieldCallback
     */
    public function percentileRanks(
        string $name,
        string $field,
        array $values,
        array $hdr = [],
        int $missing = null,
        callable $scriptFieldCallback = null
    )
    {
        $this->query[$name] = [
            "percentile_ranks" => [
                "values" => $values,
            ]
        ];

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["percentile_ranks"]["script"] = $scriptField;
        } else {

            $this->query[$name]["percentile_ranks"]["field"] = $field;

            if ($hdr) {
                $this->query[$name]["percentile_ranks"]["hdr"] = $hdr;
            }

            if ($missing) {
                $this->query[$name]["percentile_ranks"]["missing"] = $missing;
            }
        }
    }

    /**
     * A metric aggregation that executes using scripts to provide a metric output.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param null $initScript
     * @param null $mapScript
     * @param null $combineScript
     * @param null $reduceScript
     * @return $this
     */
    public function scriptedMetric(
        string $name,
        $initScript = null,
        $mapScript = null,
        $combineScript = null,
        $reduceScript = null
    )
    {
        $this->query[$name] = [
            "scripted_metric" => []
        ];

        if ($initScript) {
            $this->query[$name]["scripted_metric"]["init_script"] = $initScript;
        }

        if ($mapScript) {
            $this->query[$name]["scripted_metric"]["map_script"] = $mapScript;
        }

        if ($initScript) {
            $this->query[$name]["scripted_metric"]["combine_script"] = $combineScript;
        }

        if ($initScript) {
            $this->query[$name]["scripted_metric"]["reduce_script"] = $reduceScript;
        }

        return $this;
    }

    /**
     * A multi-value metrics aggregation that computes stats over numeric values extracted from the
     * aggregated documents. These values can be extracted either from specific numeric fields in the documents,
     * or be generated by a provided script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string|null $field
     * @param string|null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function stats(
        string $name,
        string $field = null,
        string $missing = null,
        callable $scriptFieldCallback = null
    ) {
        $this->query[$name] = [
            "stats" => []
        ];

        if ($field) {
            $this->query[$name]["stats"]["field"] = $field;
        }

        if ($missing) {
            $this->query[$name]["stats"]["field"] = $missing;
        }

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["stats"]["script"] = $scriptField;
        }

        return $this;
    }

    /**
     * A single-value metrics aggregation that sums up numeric values that are extracted from the
     * aggregated documents. These values can be extracted either from specific numeric fields in the documents,
     * or be generated by a provided script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string $field
     * @param int|null $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function sum(
        string $name,
        string $field,
        int $missing = null,
        callable $scriptFieldCallback = null
    )
    {
        $this->query[$name] = [
            "min" => [
                "field" => $field,
            ]
        ];

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["sum"]["script"] = $scriptField;
        }

        if ($missing) {
            $this->query[$name]["sum"]["missing"] = $missing;
        }

        return $this;
    }

    /**
     * A top_hits metric aggregator keeps track of the most relevant document being aggregated.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function topHits(string $name, array $params = [])
    {
        $this->query[$name] = [
            "top_hits" => $params
        ];

        return $this;
    }

    /**
     * A single-value metrics aggregation that counts the number of values that are extracted from the
     * aggregated documents. These values can be extracted either from specific fields in the documents,
     * or be generated by a provided script.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string|null $field
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function valueCount(string $name, string $field = null, callable $scriptFieldCallback = null)
    {
        $this->query[$name] = [
            "value_count" => []
        ];

        if ($field) {
            $this->query[$name]["value_count"]["field"] = $field;
        }

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["value_count"]["script"] = $scriptField;
        }

        return $this;
    }

    /**
     * This single-value aggregation approximates the median absolute deviation of its search results.
     *
     * Aggregation type: Metric
     *
     * @param string $name
     * @param string|null $field
     * @param int|null $compression
     * @param int $missing
     * @param callable|null $scriptFieldCallback
     * @return $this
     */
    public function medianAbsoluteDeviation(
        string $name,
        string $field = null,
        int $compression = null,
        int $missing,
        callable $scriptFieldCallback = null
    ) {
        $this->query[$name] = [
            "median_absolute_deviation" => []
        ];

        if ($field) {
            $this->query[$name]["median_absolute_deviation"]["field"] = $field;
        }

        if ($compression) {
            $this->query[$name]["median_absolute_deviation"]["compression"] = $compression;
        }

        if ($missing) {
            $this->query[$name]["median_absolute_deviation"]["missing"] = $missing;
        }

        if ($scriptFieldCallback) {

            $scriptField = $this->scriptField($scriptFieldCallback);

            $this->query[$name]["value_count"]["script"] = $scriptField;
        }

        return $this;
    }

    /**
     * The matrix_stats aggregation is a numeric aggregation that computes the following statistics
     * over a set of document fields.
     *
     * Aggregation type: Matrix
     *
     * @param string $name
     * @param array $fields
     * @param array $missing
     */
    public function matrixStats(string $name, array $fields, array $missing = [])
    {
        $this->query[$name] = [
            "matrix_stats" => [
                "fields" => $fields
            ]
        ];

        if ($missing) {
            $this->query[$name]["matrix_stats"]["missing"] = $missing;
        }
    }

    /**
     * To use custom aggregations which are still not supported by the package
     *
     * @param string $name
     * @param array $params
     */
    public function custom(string $name, array $params)
    {
        $this->query[$name] = $params;
    }
}