<?php

namespace PragmaRX\Tracker\Package;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot Service Provider.
     */
    public function boot()
    {
        $this->publishConfiguration();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerService();
    }

    /**
     * Configure config path.
     */
    private function publishConfiguration()
    {
        $this->publishes([
            __DIR__.'/../config/tracker.php' => config_path('tracker.php'),
        ]);
    }

    /**
     * Register service service.
     */
    private function registerService()
    {
        $this->app->singleton('pragmarx.tracker', function (Application $app) {
            return $app->make(Tracker::class);
        });
    }
}
