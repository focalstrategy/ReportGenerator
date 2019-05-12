<?php

namespace FocalStrategy\ReportGenerator\DataProviders;

use FocalStrategy\ReportGenerator\DataProviders\DataProvider;

interface PagedDataProvider extends DataProvider
{
    public function getPaged(int $start, int $length, array $ordering);

    public function getTotal();
}
