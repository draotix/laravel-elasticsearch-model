<?php

namespace Draotix\LaravelElasticsearchModels\Traits;

trait MappingFields
{
    /**
     * Add a alias field to the elastic-search map query.
     *
     * @param string $name
     * @param string $path
     * @return $this
     */
    public function alias(string $name, string $path)
    {
        $this->validateField("alias", $name, []);

        $this->mapping[$name] = [
            "type" => "alias",
            "path" => $path
        ];

        return $this;
    }

    /**
     * Add a binary field to the elastic-search map query.
     *
     * @param string $name
     * @param bool $doc_values
     * @param bool $store
     * @return $this
     */
    public function binary(string $name, bool $doc_values = false, bool $store = false)
    {
        $this->validateField("binary", $name, []);

        $this->mapping[$name] = [
            "type" => "binary",
            "doc_values" => $doc_values,
            "store" => $store,
        ];

        return $this;
    }

    /**
     * Add a integerRange field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function integerRange(string $name, array $params = [])
    {
        $this->validateField("integerRange", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "integerRange";

        return $this;
    }

    /**
     * Add a floatRange field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function floatRange(string $name, array $params = [])
    {
        $this->validateField("floatRange", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "floatRange";

        return $this;
    }

    /**
     * Add a longRange field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function longRange(string $name, array $params = [])
    {
        $this->validateField("longRange", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "longRange";

        return $this;
    }

    /**
     * Add a doubleRange field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function doubleRange(string $name, array $params = [])
    {
        $this->validateField("doubleRange", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "doubleRange";

        return $this;
    }

    /**
     * Add a dateRange field to the elastic-search map query.
     *
     * @param string $name
     * @param string $format
     * @param array $params
     * @return $this
     */
    public function dateRange(string $name, string $format = "yyyy-MM-dd HH:mm:ss", array $params = [])
    {
        $this->validateField("dateRange", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "dateRange";
        $this->mapping[$name]["format"] = $format;

        return $this;
    }

    /**
     * Add a ipRange field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function ipRange(string $name, array $params = [])
    {
        $this->validateField("ipRange", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "ipRange";

        return $this;
    }

    /**
     * Add a boolean field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function boolean(string $name, array $params = [])
    {
        $this->validateField("boolean", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "boolean";

        return $this;
    }

    /**
     * Add a date field to the elastic-search map query.
     *
     * @param string $name
     * @param string $format
     * @param string $locale
     * @param array $params
     * @return $this
     */
    public function date(string $name, string $format = "yyyy-MM-dd HH:mm:ss", string $locale = "", array $params = [])
    {
        $this->validateField("date", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "date";
        $this->mapping[$name]["format"] = $format;

        if ($locale) {
            $this->mapping[$name]["locale"] = $locale;
        }

        return $this;
    }

    /**
     * Add a geo_point field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function geoPoint(string $name, array $params = [])
    {
        $this->validateField("geo_point", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "geo_point";

        return $this;
    }

    /**
     * Add a geo_shape field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function geoShape(string $name, array $params = [])
    {
        $this->validateField("geo_shape", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "geo_shape";

        return $this;
    }

    /**
     * Add a ip field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function ip(string $name, array $params = [])
    {
        $this->validateField("ip", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "ip";

        return $this;
    }

    /**
     * Add a keyword field to the elastic-search map query.
     *
     * @param string $name
     * @param string $fields
     * @param array $params
     * @return $this
     */
    public function keyword(string $name, $fields = "", array $params = [])
    {
        $this->validateField("keyword", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "keyword";

        $this->fieldsCallback($name, $fields);

        return $this;
    }

    /**
     * Add a nested field to the elastic-search map query.
     *
     * @param string $name
     * @param string $properties
     * @param array $params
     * @return $this
     */
    public function nested(string $name, $properties = "", array $params = [])
    {
        $this->validateField("nested", $name, $params);

        $this->mapping[$name] = $params;

        $this->fieldsCallback($name, $properties, "properties");

        return $this;
    }

    /**
     * Add a long field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function long(string $name, array $params = [])
    {
        $this->validateField("numeric", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "long";

        return $this;
    }

    /**
     * Add a integer field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function integer(string $name, array $params = [])
    {
        $this->validateField("numeric", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "integer";

        return $this;
    }

    /**
     * Add a short field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function short(string $name, array $params = [])
    {
        $this->validateField("numeric", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "short";

        return $this;
    }

    /**
     * Add a byte field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function byte(string $name, array $params = [])
    {
        $this->validateField("numeric", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "byte";

        return $this;
    }

    /**
     * Add a double field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function double(string $name, array $params = [])
    {
        $this->validateField("numeric", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "double";

        return $this;
    }

    /**
     * Add a float field to the elastic-search map query.
     *
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function float(string $name, array $params = [])
    {
        $this->validateField("numeric", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "float";

        return $this;
    }

    /**
     * Add a object field to the elastic-search map query.
     *
     * @param string $name
     * @param string $properties
     * @param array $params
     * @return $this
     */
    public function object(string $name, $properties = "", array $params = [])
    {
        $this->validateField("object", $name, $params);

        $this->mapping[$name] = $params;

        $this->fieldsCallback($name, $properties, 'properties');

        return $this;
    }

    /**
     * Add a text field to the elastic-search map query.
     *
     * @param string $name
     * @param string $fields
     * @param bool $token_count
     * @param array $params
     * @return $this
     */
    public function text(string $name, $fields = "", bool $token_count = false, array $params = [])
    {
        $this->validateField("text", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]['type'] = "text";

        $this->fieldsCallback($name, $fields);

        if ($token_count) {
            $this->mapping[$name]["fields"]["token_length"] = [
                "type" => "token_count",
                "analyzer" => "standard"
            ];
        }

        return $this;
    }

    /**
     * Add a join field to the elastic-search map query.
     *
     * @param string $name
     * @param string $parent
     * @param string|array $children
     * @param array $params
     * @return $this
     */
    public function join(string $name, string $parent, $children, array $params = [])
    {
        $this->validateField("join", $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]["type"] = "join";
        $this->mapping[$name]["relations"][$parent] = $children;

        return $this;
    }

    /**
     * Add a custom field to the elastic-search map query.
     *
     * @param string $type
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function custom(string $type, string $name, array $params = [])
    {
        $this->validateField($type, $name, $params);

        $this->mapping[$name] = $params;
        $this->mapping[$name]['type'] = $type;

        return $this;
    }
}