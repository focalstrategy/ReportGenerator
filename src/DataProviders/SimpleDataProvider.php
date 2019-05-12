<?php

namespace FocalStrategy\ReportGenerator\DataProviders;

use FocalStrategy\ReportGenerator\DataProviders\DataProvider;

interface SimpleDataProvider extends DataProvider
{
    public function get();
}
