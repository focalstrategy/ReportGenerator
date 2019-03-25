<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

use FocalStrategy\Core\Renderable;

class RenderableAggregation extends AggregationRow
{
    protected $field_override;
    protected $renderable;


    public function __construct(string $field_override = null, Renderable $renderable)
    {
        $this->field_override = $field_override;
        $this->renderable = $renderable;
    }

    public function calculate(string $field_name, $data)
    {
        return $this->renderable;
    }
}
