<?php

namespace FocalStrategy\ReportGenerator\Formatters;

use FocalStrategy\Core\Renderable;
use FocalStrategy\Core\ValueInterface;
use FocalStrategy\ReportGenerator\ReportType;

class Formatter
{
    protected $field;

    public function setField(string $field)
    {
        $this->field = $field;
    }

    public function renderObject($obj, ReportType $type, string $tag = 'td') : string
    {
        $field = $this->field;
        if ($type == ReportType::PLAIN()) {
            return $this->render($obj->$field, $type);
        }

        $html_attributes = '';
        if ($obj->$field instanceof ValueInterface) {
            $attributes = $obj->$field->getAttributes();

            $html_attributes = $this->attr($attributes);
        }

        return '<'.$tag.' '.$html_attributes.'>'.$this->render($obj->$field, $type).'</'.$tag.'>';
    }

    public function render($value, ReportType $type) : string
    {
        if (!$value instanceof ValueInterface && !$value instanceof Renderable) {
            return $value ?? '';
        }

        if ($type == ReportType::PLAIN()) {
            return $value->value($type);
        }

        return $value->render($type);
    }

    private function attr(array $attributes) : string
    {
        $html_attributes = [];
        foreach ($attributes as $attr_name => $attr_value) {
            $html_attributes[] = $attr_name.'="'.$attr_value.'"';
        }

        return implode(' ', $html_attributes);
    }
}
