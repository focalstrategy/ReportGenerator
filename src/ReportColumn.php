<?php

namespace FocalStrategy\ReportGenerator;

use FocalStrategy\ReportGenerator\Formatters\Formatter;
use FocalStrategy\ReportGenerator\Formatters\NumberFormatter;

class ReportColumn
{
    protected $field_name;
    protected $display_name;
    protected $visible;
    protected $visible_when_empty;
    protected $is_date;

    protected $formatter;
    protected $is_number = false;

    public function __construct(string $field_name, string $display_name, bool $visible = true, bool $is_date = false)
    {
        $this->field_name = $field_name;
        $this->display_name = $display_name;
        $this->visible = $visible;
        $this->is_date = $is_date;

        $this->formatter = new Formatter();
        $this->formatter->setField($field_name);
    }

    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
        $this->formatter->setField($this->field_name);

        if ($formatter instanceof NumberFormatter) {
            $this->is_number = true;
        }
    }

    public function setVisibleWhenEmpty(bool $visible_when_empty)
    {
        $this->visible_when_empty = $visible_when_empty;
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function getDisplayName()
    {
        return $this->display_name;
    }

    public function getFieldName()
    {
        return $this->field_name;
    }

    public function isVisible()
    {
        return $this->visible;
    }

    public function setVisible(bool $visible)
    {
        $this->visible = $visible;
    }

    public function isVisibleWhenEmpty()
    {
        return $this->visible_when_empty;
    }

    public function isNumber()
    {
        return $this->is_number;
    }

    public function isDate()
    {
        return $this->is_date;
    }
}
