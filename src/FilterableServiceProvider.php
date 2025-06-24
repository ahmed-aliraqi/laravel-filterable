<?php

namespace AhmedAliraqi\LaravelFilterable;

use AhmedAliraqi\LaravelFilterable\Console\Commands\MakeFilterCommand;
use Illuminate\Support\ServiceProvider;

class FilterableServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFilterCommand::class,
            ]);
        }
    }
}
