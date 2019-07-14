<?php

namespace Draotix\LaravelElasticsearchModels\Helpers;


class DataTypeParameters
{
    private static $allowedParameters = [];

    /**
     * Use to validate parameter for elastic data types
     *
     * @param string $type
     * @param string $parameterName
     * @return bool
     */
    public static function isParameterValid(string $type, string $parameterName)
    {
        self::getDatatypeAllowedParameters();

        if (! isset(self::$allowedParameters[$type])) {
            throw new \InvalidArgumentException("Data type " . $type . " does not exist.");
        }

        if (! in_array($parameterName, self::$allowedParameters[$type])) {
            throw new \InvalidArgumentException("'" . $parameterName . "' is not a valid parameter for data type " . $type . ".");
        }

        return true;
    }

    /**
     * Used to validate array of elastic data type parameters.
     *
     * @param string $type
     * @param array $parameters
     */
    public static function validateParameters(string $type, array $parameters)
    {
        foreach ($parameters as $parameterName => $parameterValue) {
            self::isParameterValid($type, $parameterName);
        }
    }

    /**
     * Get fields datatype valid parameters.
     */
    private static function getDatatypeAllowedParameters()
    {
        if (! self::$allowedParameters) {
            self::$allowedParameters = config("el-allowed-parameters")['mapping'];
        }
    }

}