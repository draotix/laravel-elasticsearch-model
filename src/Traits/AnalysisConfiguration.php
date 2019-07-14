<?php

namespace Draotix\LaravelElasticsearchModels\Traits;

use Draotix\LaravelElasticsearchModels\AnalyzerBuilder;
use Draotix\LaravelElasticsearchModels\Helpers\ConfigAnalysis;

trait AnalysisConfiguration
{
    public function customAnalyzer(string $analyzerName, callable $callback)
    {
        $analyzeBuilder = $callback(new AnalyzerBuilder());

        if ($analyzeBuilder instanceof AnalyzerBuilder) {
            if (! isset($this->analysis['analyzer'])) {
                $this->analysis['analyzer'] = [];
            }

            $this->analysis['analyzer'][$analyzerName] = $analyzeBuilder->get();
        }

        return $this;
    }

    public function configAnalyzer(string $analyzer, string $analyzerName, array $params)
    {
        $this->config("analyzer", $analyzer, $analyzerName, $params);

        return $this;
    }

    public function configTokenizer(string $tokenizer, string $tokenizerName, array $params)
    {
        $this->config("tokenizer", $tokenizer, $tokenizerName, $params);

        return $this;
    }

    public function configFilter(string $filter, string $filterName, array $params)
    {
        $this->config("filter", $filter, $filterName, $params);

        return $this;
    }

    public function configCharFilter(string $charFilter, string $charFilterName, array $params)
    {
        $this->config("char_filter", $charFilter, $charFilterName, $params);

        return $this;
    }

    private function config(string $type, string $config, string $name, array $params)
    {
        ConfigAnalysis::validateAnalyzis($config, $params, $type);

        $this->analysis[$type][$name] = $params;
        $this->analysis[$type][$name]['type'] = $config;
    }
}