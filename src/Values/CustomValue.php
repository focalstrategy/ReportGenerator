<?php

namespace FocalStrategy\ReportGenerator\Values;

use FocalStrategy\Core\ValueInterface;

class CustomValue implements ValueInterface
{
    protected $value;
    protected $data;
    protected $template;

    public function __construct($value, $data, $template)
    {
        $this->value = $value;
        $this->data = $data;
        $this->template = $template;
    }

    public function value()
    {
        if (isset($this->value->manufacturer_colour) && $this->value->manufacturer_colour->name != '') {
            return $this->value->manufacturer_colour->display_name;
        } elseif (isset($this->value->colour)) {
            return $this->value->colour->name;
        }
        return 'unknown';
    }

    public function __toString()
    {
        return $this->value();
    }

    public function render() : string
    {
        return view($this->template, ['data' => $this->data])->render();
    }

    public function getAttributes() : array
    {
        return [];
    }

    public static function make(...$a)
    {
        return new static(...$a);
    }
}
