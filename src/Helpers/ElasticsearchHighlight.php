<?php

namespace Draotix\LaravelElasticsearchModels\Helpers;

use \BadMethodCallException;

class ElasticsearchHighlight
{
    private $params = [];
    private $fields = [];
    private $isField = false;

    public function __construct(bool $is_field = false)
    {
        $this->isField = $is_field;
    }

    public function setBoundaryChars(string $boundaryChars)
    {
        $this->params['boundary_chars'] = $boundaryChars;

        return $this;
    }

    public function setBoundaryMaxScan(string $boundaryMaxScan)
    {
        $this->params['boundary_max_scan'] = $boundaryMaxScan;

        return $this;
    }

    public function setBoundaryScanner(string $boundaryScanner)
    {
        $this->params['boundary_scanner'] = $boundaryScanner;

        return $this;
    }

    public function setBoundaryScannerLocale(string $boundaryScannerLocale)
    {
        $this->params['boundary_scanner_locale'] = $boundaryScannerLocale;

        return $this;
    }

    public function setEncoder(string $encoder)
    {
        $this->params['encoder'] = $encoder;

        return $this;
    }

    public function setForceSource(bool $forceSource)
    {
        $this->params['force_source'] = $forceSource;

        return $this;
    }

    public function setFragmenter(bool $fragmenter)
    {
        $this->params['fragmenter'] = $fragmenter;

        return $this;
    }

    public function setFragmentOffset(string $fragmentOffset)
    {
        $this->params['fragment_offset'] = $fragmentOffset;

        return $this;
    }

    public function setFragmentSize(int $fragmentSize)
    {
        $this->params['fragment_size'] = $fragmentSize;

        return $this;
    }

    public function setHighlightQuery(array $highlightQuery)
    {
        $this->params['highlight_query'] = $highlightQuery;

        return $this;
    }

    public function setMatchedFields(array $matchedFields)
    {
        $this->params['matched_fields'] = $matchedFields;

        return $this;
    }

    public function setNoMatchSize(int $noMatchSize)
    {
        $this->params['no_match_size'] = $noMatchSize;

        return $this;
    }

    public function setNumberOfFragments(int $numberOfFragments)
    {
        $this->params['number_of_fragments'] = $numberOfFragments;

        return $this;
    }

    public function setPhraseLimit(int $phraseLimit)
    {
        $this->params['phrase_limit'] = $phraseLimit;

        return $this;
    }

    public function setPreTags(array $preTags)
    {
        $this->params['pre_tags'] = $preTags;

        return $this;
    }

    public function setPostTags(array $postTags)
    {
        $this->params['post_tags'] = $postTags;

        return $this;
    }

    public function setRequireFieldMatch(bool $requireFieldMatch)
    {
        $this->params['require_field_match'] = $requireFieldMatch;

        return $this;
    }

    public function setTagsSchema(string $tagsSchema)
    {
        $this->params['tags_schema'] = $tagsSchema;

        return $this;
    }

    public function setType(string $type)
    {
        $this->params['type'] = $type;

        return $this;
    }

    public function field(string $name, $callback = '')
    {
        if ($this->isField) {
            throw new BadMethodCallException("Fields cannot have children");
        }

        if (! $callback) {
            $this->fields[$name] = (object)[];
        } else {
            $this->fields[$name] = $callback(new static(true))->get();
        }
    }

    public function get()
    {
        if ($this->isField) {
            return $this->params;
        } else {
            $this->params['fields'] = $this->fields;
            return $this->params;
        }
    }
}