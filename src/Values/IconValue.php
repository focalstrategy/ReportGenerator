<?php

namespace FocalStrategy\ReportGenerator\Values;

use Html;
use App\Berry\Helpers\Helpers\ValueCategorizer;

class IconValue extends Value
{
    public function __construct(string $value = null)
    {
        parent::__construct($value);
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
        return '<span class="fa ' . $this->value . '"></span>';
    }
}
