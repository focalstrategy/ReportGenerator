<?php

namespace FocalStrategy\ReportGenerator\Values;

use Html;
use App\Berry\Helpers\Helpers\ValueCategorizer;

class EmailValue extends Value
{
    protected $value;
    protected $default_sort = true;

    public function __construct(string $value = null)
    {
        parent::__construct($value);
        $this->value = $value;
    }

    /** Implementation **/
    public function value() : string
    {
        return $this->value ??   '';
    }

    public function __toString() : string
    {
        return $this->value();
    }

    public function render() : string
    {
        $html = '<a href="mailto:'.$this->value().'">'. $this->value() . '</a>';

        return $html;
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
