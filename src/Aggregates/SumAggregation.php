<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

use FocalStrategy\ReportGenerator\Values\NumberValue;
use FocalStrategy\Core\ValueInterface;

class SumAggregation extends AggregationRow
{
    protected $title = 'Total';
    protected $field_override;
    protected $decimal_places;

    public function __construct(string $field_override = null, int $decimal_places = 2)
    {
        $this->field_override = $field_override;
        $this->decimal_places = $decimal_places;
    }

    public function calculate(string $field_name, $data)
    {
        $total = 0;
        foreach ($data as $d) {
            if ($this->field_override) {
                $field_name = $this->field_override;
            }

            if ($d->{$field_name} instanceof ValueInterface) {
                if (is_numeric($d->{$field_name}->value())) {
                    $total += $d->{$field_name}->value();
                }
            } elseif (is_numeric($d->{$field_name})) {
                $total += $d->{$field_name};
            }
        }

        $nv = NumberValue::make($total)
                ->setDecimalPlaces($this->decimal_places)
                ->setTooltip('∑ '.ucwords(str_replace('_', ' ', $field_name)));

        if ($this->link) {
            $nv->link($this->link);
        }

        return $nv;
    }
}
