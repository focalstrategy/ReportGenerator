<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

class CountAggregation extends AggregationRow
{
    protected $title = 'Count';

    public function calculate(string $field_name, $data)
    {
        $count = 0;
        foreach ($data as $d) {
            if ($d->{$field_name}) {
                $count ++;
            }
        }
        return $count;
    }
}
