<?php

namespace FocalStrategy\ReportGenerator\Values;

use Html;
use App\Berry\Helpers\Helpers\ValueCategorizer;

class NumberValue extends Value
{
    protected $decimal_places = 0;
    protected $prepend = '';
    protected $append = '';
    protected $hide_zeros = false;
    protected $allow_nulls = false;

    protected $class_to_value = false;
    protected $link;

    public function __construct(float $value = null)
    {
        parent::__construct($value);
    }

    public function link(string $link)
    {
        $this->link = $link;
        return $this;
    }

    /** Options **/
    public function setDecimalPlaces(int $decimal_places)
    {
        $this->decimal_places = $decimal_places;
        return $this;
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

    public function allowNulls(bool $allow_nulls)
    {
        $this->allow_nulls = $allow_nulls;
        return $this;
    }

    public function hideZeros(bool $hide_zeros)
    {
        $this->hide_zeros = $hide_zeros;
        return $this;
    }

    public function valueCategorizer(array $class_to_value = [])
    {
        $this->class_to_value = $class_to_value;

        return $this;
    }

    /** Options - Pre Defined **/
    public function currency()
    {
        return $this->setDecimalPlaces(2);
    }

    public function percent()
    {
        return $this->setDecimalPlaces(1)->append('%');
    }

    /** Implementation **/
    public function value() /** ?float would be the ideal but only PHP 7.1 + **/
    {
        if (empty($this->value) && !$this->allow_nulls) {
            return 0;
        }

        return $this->value;
    }

    public function __toString() : string
    {
        if ($this->value == 0 && $this->hide_zeros) {
            return '';
        }

        return $this->value();
    }

    public function render() : string
    {
        if ($this->value === null) {
            return '';
        }

        if ($this->value == 0) {
            if ($this->hide_zeros) {
                return '';
            } else {
                return '<span class="text-muted">'.$this->prepend.number_format(0, $this->decimal_places).$this->append.'</span>';
            }
        }

        $result = $this->prepend.number_format((float)$this->value, $this->decimal_places).$this->append;

        if ($this->link) {
            return Html::link($this->link, $result);
        } else {
            return $result;
        }
    }

    public function getClasses() : array
    {
        $classes = parent::getClasses();
        $classes[] = 'numeric_cell';

        if ($this->class_to_value) {
            $classes[] = (new ValueCategorizer())->categorize($this->class_to_value, $this->value());
        }

        if ($this->extra_classes) {
            $classes = array_merge($classes, $this->extra_classes);
        }

        return $classes;
    }


    /** Math - Good Idea / Bad Idea? */
    public function subtract(NumberValue $nv)
    {
        return self::make($this->value() - $nv->value())
        ->setDecimalPlaces($this->decimal_places)
        ->prepend($this->prepend)
        ->append($this->append)
        ->hideZeros($this->hide_zeros);
    }

    public function add(NumberValue $nv)
    {
        return self::make($this->value() + $nv->value())
        ->setDecimalPlaces($this->decimal_places)
        ->prepend($this->prepend)
        ->append($this->append)
        ->hideZeros($this->hide_zeros);
    }

    public function multiply(NumberValue $nv)
    {
        return self::make($this->value() * $nv->value())
        ->setDecimalPlaces($this->decimal_places)
        ->prepend($this->prepend)
        ->append($this->append)
        ->setTooltip(
            (!empty($this->getTooltip()) ? '('.$this->getTooltip().')' : $this->value()).
            ' * '.
            (!empty($nv->getTooltip()) ? '('.$nv->getTooltip().')' : $nv->value())
        )
        ->hideZeros($this->hide_zeros);
    }

    public function divide(NumberValue $nv)
    {
        if ($nv->isZero() || $this->isZero()) {
            return self::make(0)->setTooltip($nv->value().' ÷ '.$this->value());
        }

        return self::make($this->value() / $nv->value())
        ->setDecimalPlaces($this->decimal_places)
        ->prepend($this->prepend)
        ->append($this->append)
        ->setTooltip(
            (!empty($this->getTooltip()) ? '('.$this->getTooltip().')' : $this->value()).
            ' ÷ '.
            (!empty($nv->getTooltip()) ? '('.$nv->getTooltip().')' : $nv->value())
        )
        ->hideZeros($this->hide_zeros);
    }

    public function calculate(NumberValue $nv, callable $calc)
    {
        $value = $calc($this->value(), $nv->value);
        return self::make($value)
        ->setDecimalPlaces($this->decimal_places)
        ->prepend($this->prepend)
        ->append($this->append)
        ->hideZeros($this->hide_zeros);
    }

    public function isZero()
    {
        return $this->value() == 0;
    }
}
