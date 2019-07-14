<?php

namespace Draotix\LaravelElasticsearchModels\Helpers;

class ScriptField
{
    private $fields = [];

    public function getField()
    {
        return $this->fields;
    }

    public function field(
        string $fieldName,
        string $lang = "",
        string $source = "",
        array $params = []
    ) {
        $this->fieldNameExist($fieldName);

        $this->fields[$fieldName] = [
            "script" => [
                "source" => $source
            ]
        ];

        if ($lang) {
            $this->fields[$fieldName]["script"]["lang"] = $lang;
        }

        if ($params) {
            $this->fields[$fieldName]["script"]["params"] = $params;
        }
    }

    public function simpleField(string $fieldName, string $script)
    {
        $this->fieldNameExist($fieldName);

        $this->fields[$fieldName] = [
            "script" => $script
        ];
    }

    private function fieldNameExist($fieldName)
    {
        if (array_key_exists($fieldName, $this->fields)) {
            throw new \InvalidArgumentException("Field name {$fieldName} already exist on script fields.");
        }
    }
}