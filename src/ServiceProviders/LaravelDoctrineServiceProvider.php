<?php

namespace Giadc\JsonApiResponse\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class LaravelDoctrineServiceProvider extends ServiceProvider
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
            'Giadc\JsonApiResponse\Interfaces\ResponseContract',
            'Giadc\JsonApiResponse\Responses\Response'
        );

        $this->app->bind(
            'Giadc\JsonApiResponse\Interfaces\PaginatorAdapter',
            'Giadc\JsonApiResponse\Pagination\FractalDoctrinePaginatorAdapter'
        );
    }
}
