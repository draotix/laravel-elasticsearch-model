<?php namespace Draotix\LaravelElasticsearchModels\Console\Commands;

class MapDown extends BaseMapCommand
{
    protected $signature = 'elasticMap:down';
    protected $description = 'Delete mapping for the models';

    public function handle()
    {
        $modelLocations = $this->getModelsLocations();

        $models = $this->getModelsWithNamespace($modelLocations);

        $this->callModelsMethods($models, "mapDown");
    }
}
