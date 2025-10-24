<?php

namespace Sudipta\Vrio;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as GuzzleClient;

class VrioServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/vrio.php', 'vrio');

        $this->app->singleton(\Sudipta\Vrio\VrioClient::class, function ($app) {
            return new \Sudipta\Vrio\VrioClient(
                new \GuzzleHttp\Client([
                    'base_uri' => rtrim(config('vrio.base_url'), '/') . '/',
                    'headers' => [
                        'Accept' => 'application/json',
                        'X-Api-Key' => config('vrio.api_key'),
                        'Content-Type' => 'application/json',
                    ]
                ])
            );
        });

        // Optional: Register alias manually (if auto-discovery fails)
        if (!class_exists('Vrio')) {
            class_alias(\Sudipta\Vrio\Facades\Vrio::class, 'Vrio');
        }
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/vrio.php' => config_path('vrio.php'),
            ], 'vrio-config');
        }
    }
}
