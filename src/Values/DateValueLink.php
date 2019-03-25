<?php

namespace FocalStrategy\ReportGenerator\Values;

use Html;
use Carbon\Carbon;

class DateValueLink extends DateValue
{
    protected $value;
    protected $link;

    public function __construct(Carbon $value = null, string $format = 'd/m/Y', string $link)
    {
        $this->value = $value;
        $this->format = $format;
        $this->link = $link;
    }

    public function value()
    {
        if (!$this->value) {
            return '';
        }

        return $this->value->timezone('Europe/London')->format($this->format);
    }

    public function __toString() : string
    {
        return $this->value();
    }

    public function render() : string
    {
        return Html::link($this->link, $this->value());
    }
}
