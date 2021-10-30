<?php

namespace GustavoSantarosa\LaravelToolPack\Provider;

use Illuminate\Support\ServiceProvider;

class LaravelToolPackProvider extends ServiceProvider
{
    public $bindings = [
        ServerProvider::class => DataTransferObject::class,
        ServerProvider::class => ReturnPrepare::class,
    ];

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
        //
    }
}
