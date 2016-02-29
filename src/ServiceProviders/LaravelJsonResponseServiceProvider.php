<?php

namespace Giadc\JsonResponse\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class LaravelJsonResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Giadc\JsonResponse\Interfaces\ResponseContract',
            'Giadc\JsonResponse\Responses\LaravelResponse'
        );
    }
}
