<?php

namespace FocalStrategy\ReportGenerator\Values;

use Carbon\Carbon;
use App\Berry\Helpers\Helpers\ValueCategorizer;
use FocalStrategy\ReportGenerator\ReportType;
use FocalStrategy\Core\ValueInterface;

class TimeValue implements ValueInterface
{
    protected $format;
    protected $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value()
    {
        if (!$this->value) {
            return '';
        }
        $value = explode(':', $this->value);
        array_pop($value);
        return implode(':', $value);
    }

    public function __toString()
    {
        return $this->value();
    }

    public function render() : string
    {
        return $this->value();
    }

    public function getAttributes() : array
    {
        if (!$this->value) {
            return [];
        }

        return [
            'data-sort' => $this->value,
            'class' => implode(' ', $this->getClasses())
        ];
    }

    public function getClasses() : array
    {
        $classes = [];

        return $classes;
    }


    public static function make(...$a)
    {
        return new static(...$a);
    }
}
