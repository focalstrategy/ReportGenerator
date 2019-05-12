<?php

namespace FocalStrategy\ReportGenerator;

use Illuminate\Support\ServiceProvider;

class ReportGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'report_generator');
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/report_generator'),
        ]);

        $this->publishes([
            __DIR__.'/public' => public_path('vendor/focalstrategy/report_generator'),
        ], 'public');
    }
}
