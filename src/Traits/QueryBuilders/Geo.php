<?php

namespace Draotix\LaravelElasticsearchModels\Traits\QueryBuilders;

trait Geo
{
    /**
     * Uses the same grid square representation as the geo_shape mapping to find documents that have a
     * shape that intersects with the query shape.
     *
     * @param string $field
     * @param string $shapeType
     * @param array $coordinates
     * @param string $relation
     * @return $this
     */
    public function geoShape(string $field, string $shapeType, array $coordinates, string $relation = "")
    {
        $query = [
            "shape" => [
                "type" => $shapeType,
                "coordinates" => $coordinates
            ],
        ];

        if ($relation) {
            $query['relation'] = $relation;
        }

        $this->query['geo_shape'] = [
            $field => $query
        ];

        return $this;
    }

    /**
     * Shape which has already been indexed in another index.
     *
     * @param string $field
     * @param string $index
     * @param string $id
     * @param string $path
     * @return $this
     */
    public function getPreIndexShape(string $field, string $index, string $id, string $path)
    {
        $this->query['geo_shape'] = [
            $field => [
                "indexed_shape" => [
                    "index" => $index,
                    "id" => $id,
                    "path" => $path
                ]
            ]
        ];

        return $this;
    }

    /**
     * A query allowing to filter hits based on a point location using a bounding box.
     *
     * @param string $field
     * @param array $params
     * @return $this
     */
    public function geoBoundingBox(string $field, array $params)
    {
        $this->query['geo_shape'] = [
            $field => $params
        ];
        return $this;
    }

    /**
     * Filters documents that include only hits that exists within a specific distance from a geo point.
     *
     * @param string $field
     * @param string $distance
     * @param int $lat
     * @param int $lon
     * @return $this
     */
    public function geoDistance(string $field, string $distance, int $lat, int $lon)
    {
        $this->query['geo_distance'] = [
            "distance" => $distance,
            $field => [
                "lat" => $lat,
                "lon" => $lon
            ]
        ];
        return $this;
    }

    /**
     * A query returning hits that only fall within a polygon of points.
     *
     * @param string $field
     * @param array $points
     * @return $this
     */
    public function geoPolygon(string $field, array $points)
    {
        $this->query['geo_polygon'] = [
            $field => [
                "points" => $points
            ]
        ];
        return $this;
    }
}