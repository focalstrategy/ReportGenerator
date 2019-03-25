<?php

namespace FocalStrategy\ReportGenerator\Aggregates;

use FocalStrategy\Core\Buttons\Button;
use FocalStrategy\Core\Renderable;
use FocalStrategy\ReportGenerator\ReportType;
use FocalStrategy\ReportGenerator\Values\NumberValue;
use FocalStrategy\ReportGenerator\Values\NumberValueLink;
use FocalStrategy\Core\ValueInterface;

abstract class AggregationRow
{
    protected $title = 'Generic';
    protected $aggregate = [];
    protected $calculation_override = [];
    protected $prepend;
    protected $append;
    protected $link;

    public function aggregate($to_aggregate)
    {
        if (is_array($to_aggregate)) {
            $this->aggregate = array_merge($this->aggregate, $to_aggregate);
        } else {
            $this->aggregate[] = $to_aggregate;
        }

        return $this;
    }

    public function setPrepend(string $prepend) : AggregationRow
    {
        $this->prepend = $prepend;
        return $this;
    }

    public function setAppend(string $append) : AggregationRow
    {
        $this->append = $append;
        return $this;
    }

    public function setLink(string $link) : AggregationRow
    {
        $this->link = $link;
        return $this;
    }

    public function editCalculation(string $field, callable $override)
    {
        $this->calculation_override[$field] = $override;

        return $this;
    }

    public function generate($fields, $data, ReportType $report_type)
    {
        $results = [];
        $skip_field = '';
        foreach ($fields as $i => $f) {
            if (array_search($i, array_keys($fields)) == 0) {
                $skip_field = $i;
                continue;
            }

            if (count($this->aggregate) == 0 || in_array($f->getFieldName(), $this->aggregate)) {
                $value = null;

                if (isset($this->calculation_override[$f->getFieldName()])) {
                    $value =
                    $this->calculation_override[$f->getFieldName()]($f->getFieldName(), $data);
                } else {
                    $value = $this->calculate($f->getFieldName(), $data);
                }

                $formatter = $f->getFormatter();
                $obj = new \stdClass();

                if (!$value instanceof ValueInterface) {
                    if (is_numeric($value)) {
                        $nv = NumberValue::make($value)
                                ->setDecimalPlaces(2);

                        if ($this->link) {
                            $nv->link($this->link);
                        }
                        $obj->{$f->getFieldName()} = $nv;
                    } else {
                        $obj->{$f->getFieldName()} = '';
                    }
                } else {
                    $obj->{$f->getFieldName()} = $value;
                }

                if ($value instanceof Renderable && $value instanceof Button) {
                    $obj->{$f->getFieldName()} = $value->render();
                }

                $value = $formatter->renderObject($obj, $report_type);

                $results[$f->getFieldName()] = $value;
            }
        }

        return [
            'title' => $this->title,
            'data' => $results,
            'skip' => $skip_field,
        ];
    }

    abstract public function calculate(string $field_name, $data);
}
