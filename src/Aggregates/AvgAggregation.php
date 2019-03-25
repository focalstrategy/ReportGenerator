<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

use FocalStrategy\ReportGenerator\Values\NumberValue;
use FocalStrategy\ReportGenerator\Values\NumberValueLink;
use FocalStrategy\Core\ValueInterface;

class AvgAggregation extends AggregationRow
{
    protected $title = 'Average';
    protected $total_field_override;

    public function __construct(string $count_override = null, string $total_field_override = null)
    {
        $this->total_field_override = $total_field_override;
        $this->count_override = $count_override;
    }

    public function calculate(string $field_name, $data)
    {
        if ($this->total_field_override) {
            $field_name = $this->total_field_override;
        }

        $total = 0;
        $count = 0;
        foreach ($data as $d) {
            if ($d->{$field_name} instanceof ValueInterface) {
                $total += $d->{$field_name}->value();
            } else {
                $total += $d->{$field_name};
            }

            if ($this->count_override) {
                if ($d->{$this->count_override} instanceof ValueInterface) {
                    $count += $d->{$this->count_override}->value();
                } else {
                    $count += $d->{$this->count_override};
                }
            } else {
                $count++;
            }
        }

        if ($total == 0 || $count == 0) {
            return 0;
        }

        $result = $total / $count;
        $avg = null;
        if ($this->link) {
            $avg = NumberValueLink::make($result, $this->link)
            ->setDecimalPlaces(2)
            ->setTooltip(number_format($total, 2).' / '.number_format($count));
        } else {
            $avg = NumberValue::make($result)
            ->setDecimalPlaces(2)
            ->setTooltip(number_format($total, 2).' / '.number_format($count));
        }

        if (!empty($this->prepend)) {
            $avg = $avg->prepend($this->prepend);
        }
        if (!empty($this->append)) {
            $avg = $avg->append($this->append);
        }
        return $avg;
    }
}
