<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (false && config('app.debug')) {
            \DB::listen(function ($query) {
                \Log::info(print_r([
                    'sql'      => $query->sql,
                    'bindings' => $query->bindings,
                    'time'     => $query->time,
                ], true));
            });
        }
    }
}
