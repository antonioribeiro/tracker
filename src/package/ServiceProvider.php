<?php

namespace PragmaRX\Tracker\Package;

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
     * @return string
     */
    protected function getConfigFile()
    {
        return config_path('tracker.yml');
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
            __DIR__.'/../config/tracker.yml' => $this->getConfigFile(),
        ]);
    }

    /**
     * Register service service.
     */
    private function registerService()
    {
        $this->app->singleton('pragmarx.tracker', function () {
            //$tracker = new Tracker();

            //$tracker->loadConfig($this->getConfigFile());t
        });
    }
}
