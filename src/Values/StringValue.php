<?php

namespace FocalStrategy\ReportGenerator\Values;

use Html;
use App\Berry\Helpers\Helpers\ValueCategorizer;

class StringValue extends Value
{
    protected $prepend = '';
    protected $append = '';
    protected $class_to_value = false;

    public function __construct(string $value = null)
    {
        parent::__construct($value);
    }


    public function prepend(string $prepend)
    {
        $this->prepend = $prepend;
        return $this;
    }

    public function append(string $append)
    {
        $this->append = $append;
        return $this;
    }

    public function valueCategorizer(array $class_to_value = [])
    {
        $this->class_to_value = $class_to_value;

        return $this;
    }

    /** Implementation **/
    public function value() : string
    {
        if (empty($this->value)) {
            return '';
        }

        return $this->value;
    }

    public function __toString() : string
    {
        return $this->value();
    }

    public function render() : string
    {
        return $this->value;
    }

    public function getClasses() : array
    {
        $classes = parent::getClasses();

        // if ($this->class_to_value) {
        //     $classes[] = (new ValueCategorizer())->categorize($this->class_to_value, $this->value());
        // }

        if ($this->extra_classes) {
            $classes = array_merge($classes, $this->extra_classes);
        }

        return $classes;
    }
}
