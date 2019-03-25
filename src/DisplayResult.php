<?php

namespace FocalStrategy\ReportGenerator;

use FocalStrategy\Core\Renderable;

class DisplayResult implements Renderable
{
    protected $value;
    protected $value_target;
    protected $cell_class;
    protected $collapse;

    public function __construct($value = null, $value_target = null, $cell_class = null, bool $collapse = false)
    {
        $this->value = $value;
        $this->value_target = $value_target;
        $this->cell_class = $cell_class;
        $this->collapse = $collapse;
    }

    public function render()
    {
        return view('_components.display_cell')
            ->with('value', $this->format($this->value))
            ->with('value_target', $this->format($this->value_target))
            ->with('cell_class', $this->cell_class)
            ->with('collapse', $this->collapse)
            ->render();
    }

    public function getAppend()
    {
        return null;
    }

    private function format($value)
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return view('_components.comparison_table')
                ->with('table', $value)->render();
        }

        if ($value != strip_tags($value)) {
            return $value;
        }

        return str_replace(' ', '&nbsp;', ucwords(str_replace('_', ' ', $value)));
    }
}
