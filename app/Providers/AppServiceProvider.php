<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() && ! in_array('SystemRoot', ServeCommand::$passthroughVariables, true)) {
            ServeCommand::$passthroughVariables[] = 'SystemRoot';
        }
    }
}
