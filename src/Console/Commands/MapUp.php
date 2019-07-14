<?php namespace Draotix\LaravelElasticsearchModels\Console\Commands;

class MapUp extends BaseMapCommand
{
    protected $signature = 'elasticMap:up';
    protected $description = 'Create mapping for all models';

    public function handle()
    {
        $modelLocations = $this->getModelsLocations();

        $models = $this->getModelsWithNamespace($modelLocations);

        $this->callModelsMethods($models, "mapUp");
    }

}
