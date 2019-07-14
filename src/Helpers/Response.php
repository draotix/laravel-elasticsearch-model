<?php

namespace Draotix\LaravelElasticsearchModels\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Response implements \Iterator
{
    private $took;
    private $timedOut;
    private $shards;
    private $total;
    private $maxScore;
    private $size;
    private $from;
    private $data = [];
    private $position = 0;

    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * @return mixed
     */
    public function getTook()
    {
        return $this->took;
    }

    /**
     * @return mixed
     */
    public function getTimedOut()
    {
        return $this->timedOut;
    }

    /**
     * @return mixed
     */
    public function getShards()
    {
        return $this->shards;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return mixed
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Handle response sent from the search builder.
     *
     * @param Model $model
     * @param array $response
     * @param int|null $from
     * @param int|null $size
     * @return Response
     */
    public static function handleResponse(Model $model, array $response, int $from = null, int $size = null)
    {
        $hits = [];
        if (Arr::has($response, "hits.hits")) {
            $hits = $response['hits']['hits'];
        }

        $self = new self();
        $self->data = $self->hydrate($model, $hits);
        $self->took = $response['took'] ?? null;
        $self->timedOut = $response['timed_out'] ?? null;
        $self->shards = $response['_shards'] ?? null;
        $self->maxScore = $response['hits']['max_score'] ?? null;
        $self->from = $from;
        $self->size = $size;

        if (Arr::has($response, "hits.total")) {
            $self->total = $response['hits']['total'];
        } elseif (Arr::has($response, "count")) {
            $self->total = $response['count'];

        }
        return $self;
    }

    /**
     * Create models collection from array.
     *
     * @param Model $model
     * @param $hits
     * @return array
     */
    private function hydrate(Model $model, $hits)
    {
        $primaryKey = $model->primaryKey ?: "id";

        $data = [];

        foreach ($hits as $item) {
            $model = $model->newInstance();

            $modelData = $this->handleCasts(
                $item['_source'],
                $model->getCasts()
            );

            $model->setRawAttributes($modelData);

            $model->$primaryKey = $item['_id'];
            $model->_setScore($item['_type']);
            $model->_setType($item['_score']);

            $data[] = $model;
        }

        return collect($data);
    }

    /**
     * Prepare data for laravel casting.
     *
     * @param $data
     * @param null $casts
     * @return mixed
     */
    private function handleCasts($data, $casts = null)
    {
        if ($casts) {
            foreach ($casts as $field => $castTo) {
                if (! empty($data[$field])) {

                    switch ($castTo) {
                        case "array":
                        case "object":
                            $data[$field] = json_encode($data[$field]);
                            break;
                        case "date":
                        case "datetime":
                            if (is_array($data[$field])) {
                                $datetime = new \DateTime(
                                    $data[$field]['date'],
                                    new \DateTimeZone($data[$field]['timezone'])
                                );

                                $data[$field] = $datetime->format("Y-m-d H:i:s");
                            }
                            break;
                        default:
                            break;
                    }

                }
            }
        }

        return $data;
    }

    /**
     * Check if there is any data from the response.
     *
     * @return bool
     */
    public function hasData()
    {
        if (empty($this->data)) {
            return false;
        }

        return true;
    }

    /**
     * Used in case of using response as iterator, return the current item.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->data[$this->position];
    }

    /**
     * Used in case of using response as iterator, prepares position for the next item.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Used in case of using response as iterator, get the current position.
     *
     * @return int|mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Used in case of using response as iterator, check if there is any data in the current position.
     * @return bool
     */
    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    /**
     * Used in case of using response as iterator, refreshes the state of iterator.
     */
    public function rewind()
    {
        $this->position = 0;
    }
}