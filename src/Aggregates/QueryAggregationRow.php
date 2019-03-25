<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

use FocalStrategy\ReportGenerator\FactoryRequired;
use FocalStrategy\ReportGenerator\ReportType;

class QueryAggregationRow extends AggregationRow implements FactoryRequired
{
    protected $title = 'Average';
    protected $agg_rows = [];
    protected $factory;
    protected $factory_method;
    protected $agg_result;

    public function __construct($title, $factory_method)
    {
        $this->title = $title;
        $this->factory_method = $factory_method;
    }

    public function setFactory($factory)
    {
        $this->factory = $factory;
    }

    public function getData()
    {
        $method = $this->factory_method;
        $this->agg_result = $this->factory->$method();
    }

    public function calculate(string $field_name, $data)
    {
        if (!$this->agg_result) {
            $this->getData();
        }

        if (isset($this->agg_result->$field_name)) {
            return $this->agg_result->$field_name;
        }
        return null;
    }
}
