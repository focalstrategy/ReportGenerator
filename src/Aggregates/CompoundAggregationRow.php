<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

class CompoundAggregationRow extends AggregationRow
{
    protected $title = 'Average';
    protected $agg_rows = [];

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function addAggregate($fields, AggregationRow $row)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $row->aggregate($fields);
        $this->aggregate($fields);

        foreach ($fields as $field_name) {
            $this->agg_rows[$field_name] = $row;
        }

        return $this;
    }

    public function calculate(string $field_name, $data)
    {
        if (isset($this->agg_rows[$field_name])) {
            return $this->agg_rows[$field_name]->calculate($field_name, $data);
        }
        return null;
    }
}
