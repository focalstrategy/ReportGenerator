<?php

namespace FocalStrategy\ReportGenerator\Values;

class BooleanValue extends Value
{
    protected $value;
    protected $default_sort = true;

    public function __construct(bool $value = null, bool $colour = true)
    {
        parent::__construct($value);
        $this->value = $value;
        $this->colour = $colour;
    }

    /** Implementation **/
    public function value() : bool
    {
        return $this->value ? true : false;
    }

    public function __toString() : string
    {
        return $this->value();
    }

    public function render() : string
    {
        if ($this->colour) {
            return  '<span class="colour_ticks_crosses">' . tertiaryToTickCross($this->value()) . '<span>';
        }
        return tertiaryToTickCross($this->value());
    }

    public function getAttributes() : array
    {
        if ($this->default_sort) {
            if (!$this->value) {
                return [];
            }
        }

        return [
            'data-sort' => $this->value,
            'class' => implode(' ', $this->getClasses())
        ];
    }

    public function getClasses() : array
    {
        $classes = parent::getClasses();

        if ($this->extra_classes) {
            $classes = array_merge($classes, $this->extra_classes);
        }

        return $classes;
    }

    public function setDefaultSort(bool $default_sort = true)
    {
        $this->default_sort = $default_sort;
        return $this;
    }
}
