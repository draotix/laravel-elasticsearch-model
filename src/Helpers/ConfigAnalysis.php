<?php

namespace Draotix\LaravelElasticsearchModels\Helpers;

class ConfigAnalysis
{
    /**
     * Check if a build-in analyzer with the $name exits
     *
     * @param string $name
     * @param string $type
     * @return bool
     */
    public static function analyzisExists(string $name, string $type = 'analyzers')
    {
        $analyzerConfigs = config('el-allowed-parameters')[$type];

        if (! array_key_exists($name, $analyzerConfigs)) {
            throw new \InvalidArgumentException("Analyzer called {$name} does not exit");
        }

        return true;
    }

    public static function isConfigurable(string $name, string $type)
    {
        $analyzerConfigs = config('el-allowed-parameters')[$type];

        if (! count($analyzerConfigs[$name])) {
            throw new \InvalidArgumentException("{$type} {$name} cannot be configured");
        }

        return true;
    }

    public static function validateParams(string $name, array $params, string $type)
    {
        $analyzerConfigs = config('el-allowed-parameters')[$type];

        foreach ($params as $parameterKey => $param) {
            if (! in_array($parameterKey, $analyzerConfigs[$name])) {
                throw new \InvalidArgumentException("{$parameterKey} is a invalid parameter for {$name} analyzer");
            }
        }

        return true;
    }

    public static function validateAnalyzis(string $name, $params, string $type)
    {
        if (self::analyzisExists($name, $type) &&
            self::isConfigurable($name, $type) &&
            self::validateParams($name, $params, $type)) {

            return true;
        }

        return false;
    }
}