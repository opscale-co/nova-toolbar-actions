<?php

namespace Opscale\NovaToolbarActions;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-toolbar-actions', __DIR__ . '/../dist/js/tool.js');
            Nova::style('nova-toolbar-actions', __DIR__ . '/../dist/css/tool.css');
        });
    }

    public function register()
    {
        //
    }
}
