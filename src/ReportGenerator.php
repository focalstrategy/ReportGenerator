<?php

namespace FocalStrategy\ReportGenerator;

use App;
use Factory;
use FocalStrategy\Actions\Core\ActionRenderType;
use FocalStrategy\Core\ValueInterface;
use FocalStrategy\ReportGenerator\Aggregates\AggregationRow;
use FocalStrategy\ReportGenerator\Aggregates\SumAggregation;
use FocalStrategy\ReportGenerator\DataProviders\DataProvider;
use FocalStrategy\ReportGenerator\Formatters\Formatter;
use FocalStrategy\ReportGenerator\ReportType;
use FocalStrategy\ReportGenerator\Values\Value;
use Illuminate\Support\Collection;

class ReportGenerator
{
    protected $provider;

    protected $filter;
    protected $range;

    protected $async = false;
    protected $async_route;

    protected $fields = [];
    protected $aggregates = [];

    protected $settings = [
        'aggregate'=> true,
        'id_name'=> 'NO',
    ];

    protected $actions = [];

    public function __construct(DataProvider $provider)
    {
        $this->provider = $provider;
    }

    public function setAsync(bool $async, string $async_route)
    {
        $this->async = $async;
        $this->async_route = $async_route;
    }

    public function addAggregationRow(AggregationRow $aggregate_row)
    {
        $this->aggregates[] = $aggregate_row;
    }

    public function settings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    public function col(string $display_name, string $field_name)
    {
        $this->fields[$field_name] = new ReportColumn($field_name, $display_name);
        $this->fields[$field_name]->setFormatter(new Formatter());
        $this->fields[$field_name]->setVisibleWhenEmpty(false);

        return $this->fields[$field_name];
    }

    public function action(string $action, string $hidden_if = null)
    {
        $this->actions[] = [
            'classname' => $action,
            'hidden_if' => $hidden_if,
            'replace_when' => null // used in switch
        ];
    }

    public function actionSwitch(string $field_name, string $action, string $action_alt)
    {
        $this->actions[] = [
            'classname' => $action,
            'classname_alt' => $action_alt,
            'replace_when' => $field_name,
            'hidden_if' => null // used in action
        ];
    }

    public function generate()
    {
        if ($this->async) {
            foreach ($this->actions as $i => $action) {
                $this->fields['action_'.$i] =  new ReportColumn('action_'.$i, '');
                // $this->fields['action_'.$i]->setSortable(false);
            }

            return [
                'structure' => $this->fields,
                'async_route' => $this->async_route
            ];
        }

        $provider = $this->getDataProvider();
        $results = $provider->get();

        if ($results === null) {
            return [];
        }
        foreach ($this->fields as $field) {
            $empty = true;
            foreach ($results as $row) {
                $value = $row->{$field->getFieldName()};
                if ($value instanceof ValueInterface && $value->value()) {
                    $empty = false;
                } elseif ($value) {
                    $empty = false;
                }
            }

            if ($empty && $field->isVisibleWhenEmpty()) {
                $field->setVisible(false);
            }
        }

        $totals = $this->getTotals($results, ReportType::HTML(), $provider);
        $results = $this->format($results, ReportType::HTML());

        return [
            'structure' => $this->fields,
            'data' => $results,
            'aggregate' => $totals,
            // debug
            'provider' => get_class($this->provider)
        ];
    }

    public function async($data)
    {
        $response = [
            'draw' => $data['draw'],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ];

        $ordering = [];
        if (isset($data['order'])) {
            $keys = array_keys($this->fields);
            foreach ($data['order'] as $order) {
                if ($order['column'] < count($keys)) {
                    $ordering[] = [
                        'field' => $keys[$order['column']],
                        'dir' => $order['dir']
                    ];
                }
            }
        }

        $provider = $this->getDataProvider();
        $results = $provider->getPaged(
            $data['start'],
            $data['length'],
            $ordering
        );

        foreach ($results as $row) {
            foreach ($this->actions as $i => $action) {
                $hidden_if = $action['hidden_if'];
                $replace_when = $action['replace_when'];

                if ($hidden_if && isset($row->$hidden_if)) {
                    $row->{'action_'.$i} = '<td>'.$row->$hidden_if.'</td>';
                } elseif ($replace_when && isset($row->$replace_when)) {
                    $action_classname = $action['classname'];
                    if ($row->$replace_when) {
                        $action_classname = $action['classname_alt'];
                    }

                    $actObj = App::make($action_classname);
                    $actObj->setRenderType(ActionRenderType::INLINE());
                    $actObj->addDefaultData($row->raw());

                    $row->{'action_'.$i} = '<td>'.$actObj->render().'</td>';
                } else {
                    $actObj = App::make($action['classname']);
                    $actObj->setRenderType(ActionRenderType::INLINE());
                    $actObj->addDefaultData($row->raw());

                    $row->{'action_'.$i} = '<td>'.$actObj->render().'</td>';
                }
            }
        }

        foreach ($this->fields as $field) {
            if (starts_with($field->getFieldName(), 'action_')) {
                continue;
            }

            $empty = true;
            foreach ($results as $row) {
                $value = $row->{$field->getFieldName()};
                if ($value instanceof ValueInterface && $value->value()) {
                    $empty = false;
                } elseif ($value) {
                    $empty = false;
                }
            }

            if ($empty && $field->isVisibleWhenEmpty()) {
                $field->setVisible(false);
            }
        }

        $process = [];
        foreach ($results as $row) {
            $r = [];
            foreach ($this->fields as $field) {
                if ($row->{$field->getFieldName()} instanceof ValueInterface) {
                    $r[] = $row->{$field->getFieldName()}->render();
                } else {
                    $r[] = $row->{$field->getFieldName()};
                }
            }

            foreach ($this->actions as $i => $action) {
                $r[] = $row->{'action_'.$i};
            }

            $process[] = $r;
        }

        $total = $provider->getTotal();

        $response['data'] = $process;
        $response['recordsTotal'] = $total;
        $response['recordsFiltered'] = $total;

        return $response;
    }

    private function format($results, ReportType $type)
    {
        foreach ($results as $row) {
            if ($type == ReportType::HTML()) {
                foreach ($this->actions as $i => $action) {
                    $hidden_if = $action['hidden_if'];
                    $replace_when = $action['replace_when'];

                    if ($hidden_if && isset($row->$hidden_if)) {
                        $row->{'action_'.$i} = '<td>'.$row->$hidden_if.'</td>';
                    } elseif ($replace_when && isset($row->$replace_when)) {
                        $action_classname = $action['classname'];
                        if ($row->$replace_when) {
                            $action_classname = $action['classname_alt'];
                        }

                        $actObj = App::make($action_classname);
                        $actObj->setRenderType(ActionRenderType::INLINE());
                        $actObj->addDefaultData($row->raw());

                        $row->{'action_'.$i} = '<td>'.$actObj->render().'</td>';
                    } else {
                        $actObj = App::make($action['classname']);
                        $actObj->setRenderType(ActionRenderType::INLINE());
                        $actObj->addDefaultData($row->raw());

                        $row->{'action_'.$i} = '<td>'.$actObj->render().'</td>';
                    }

                    if (!isset($this->field['action_'.$i])) {
                        $this->fields['action_'.$i] =  new ReportColumn('action_'.$i, '');
                    }
                }
            }

            foreach ($this->fields as $field) {
                if (starts_with($field->getFieldName(), 'action_')) {
                    continue;
                }

                $value = $field->getFormatter()->renderObject($row, $type);
                $row->{$field->getFieldName()} = $value;
            }
        }

        return $results;
    }

    private function getTotals(Collection $results, ReportType $type, $provider) : array
    {
        $totals = [];

        $aggregates = $this->aggregates;
        if ($this->getSettingValue('aggregate')) {
            if (count($aggregates) == 0) {
                $aggregates[] = new SumAggregation();
            }

            foreach ($aggregates as $ag) {
                if ($ag instanceof FactoryRequired) {
                    $ag->setFactory($provider);
                }
                $totals[] = $ag->generate($this->fields, $results, $type);
            }
        }
        return $totals;
    }

    public function toArray()
    {
        $results = $this->getDataProvider()->get();
        $results = $this->format($results, ReportType::PLAIN());

        $flat = [];
        $idx = 1;
        foreach ($results as $r) {
            if ($this->getSettingValue('id_name') !== null) {
                $record = [$this->getSettingValue('id_name') => $idx];
            }
            foreach ($this->fields as $field) {
                if (isset($this->fields[$field->getFieldName()])) {
                    $record[$field->getDisplayName()] = $r->{$field->getFieldName()};
                }
            }

            $flat[] = $record;
            $idx++;
        }

        return $flat;
    }

    private function getSettingValue($key)
    {
        return $this->settings[$key];
    }

    public function getDataProvider()
    {
        return $this->provider;
    }
}
