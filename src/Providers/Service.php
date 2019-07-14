<?php namespace Draotix\LaravelElasticsearchModels\Providers;

use Draotix\LaravelElasticsearchModels\Console\Commands\MapDown;
use Draotix\LaravelElasticsearchModels\Console\Commands\MapUp;
use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{
    public function boot()
    {
        $datatypesParametersConfig = __DIR__ . '/../../config/el_allowed_parameters.php';
        $this->mergeConfigFrom($datatypesParametersConfig, 'el-allowed-parameters');

        $this->commands(MapUp::class);
        $this->commands(MapDown::class);
    }
}
