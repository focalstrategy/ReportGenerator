<?php

namespace FocalStrategy\ReportGenerator;

use Illuminate\Support\ServiceProvider;
use View;

class Laravel4ReportGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('focalstrategy/report_generator');
        View::addNamespace('report_generator', __DIR__.'/resources/views_4');
    }
}
