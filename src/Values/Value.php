<?php

namespace FocalStrategy\ReportGenerator\Values;

use FocalStrategy\Core\Renderable;
use FocalStrategy\ReportGenerator\ReportType;
use FocalStrategy\Core\ValueInterface;

class Value implements ValueInterface
{
    protected $value;
    protected $tooltip;
    protected $extra_classes = [];

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function addExtraClass(string $cl)
    {
        $this->extra_classes[] = $cl;

        return $this;
    }

    public function setTooltip(string $tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip()
    {
        return $this->tooltip;
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value();
    }

    public function render() : string
    {
        return $this->value;
    }

    public function getClasses() : array
    {
        return [];
    }

    public function getAttributes() : array
    {
        $attr = [];

        if ($this->tooltip) {
            $attr['data-toggle'] = 'tooltip';
            $attr['data-title'] = $this->tooltip;
        }

        $attr['class'] = implode(' ', $this->getClasses());

        return $attr;
    }

    public static function make(...$a)
    {
        return new static(...$a);
    }
}
