<?php

namespace Draotix\LaravelElasticsearchModels\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BaseMapCommand extends Command
{
    /**
     * Get all models locations (namespaces and paths) including custom models from the configuration "config/app.php"
     *
     * @return array
     */
    protected function getModelsLocations() : array
    {
        $usualModelLocation = [
            "namespace" => "App\\",
            "path" => app_path() . "/"
        ];

        $modelLocations = [
            "usual_model_location" => $usualModelLocation
        ];

        $custom_model_path = config("app.custom_model_locations");

        if ($custom_model_path) {
            $modelLocations = array_merge($modelLocations, $custom_model_path);
        }

        return $modelLocations;
    }

    /**
     * Get all models from the configured locations
     *
     * @param array $modelLocations
     * @return array
     */
    protected function getModelsWithNamespace(array $modelLocations) : array
    {
        $modelNames = [];

        foreach ($modelLocations as $modelLocation) {
            $modelFiles = File::files($modelLocation['path']);

            foreach ($modelFiles as $modelFile) {
                $filename = $modelFile->getFilename();

                $modelNames[] = $modelLocation['namespace'] . substr($filename,0,-4);
            }
        }
        return $modelNames;
    }

    /**
     * Call "mapUp" methods of all models that has this method
     *
     * @param array $models
     * @param string $method
     */
    protected function callModelsMethods(array $models, string $method) : void
    {
        foreach ($models as $modelName) {
            if (method_exists($modelName, $method)) {
                $modelName::$method();
            }
        }
    }

}