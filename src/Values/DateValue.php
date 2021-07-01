<?php

namespace FocalStrategy\ReportGenerator\Values;

use Carbon\Carbon;
use FocalStrategy\Core\ValueInterface;
use FocalStrategy\ReportGenerator\ReportType;
use App\Berry\Helpers\Helpers\ValueCategorizer;

class DateValue implements ValueInterface
{
    const DATE_FORMAT = 'd/m/Y';
    const DATE_TIME_FORMAT = 'd/m/Y H:i';

    protected $format;
    protected $value;

    protected $class_to_value = false;

    public function __construct(Carbon $value = null, string $format = 'd/m/Y')
    {
        $this->value = $value;
        $this->format = $format;
    }

    public function value()
    {
        if (!$this->value) {
            return '';
        }
        return $this->value->timezone('Europe/London')->format($this->format);
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
        return [
            'data-sort' => $this->value ? $this->value->format('U') : 0,
            'class' => implode(' ', $this->getClasses())
        ];
    }

    public function getClasses() : array
    {
        $classes = [];
        if ($this->class_to_value) {
            $classes[] = (new ValueCategorizer())->dates($this->class_to_value, $this->value);
        }

        return $classes;
    }

    public function valueCategorizer(array $class_to_value = [])
    {
        $this->class_to_value = $class_to_value;

        return $this;
    }


    public static function make(...$a)
    {
        return new static(...$a);
    }
}
