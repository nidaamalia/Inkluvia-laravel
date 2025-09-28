<?php
// app/Providers/MqttServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\MqttHelper;

class MqttServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mqtt', function ($app) {
            return new MqttHelper();
        });
    }

    public function boot()
    {
        //
    }
}