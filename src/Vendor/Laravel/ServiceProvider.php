<?php namespace PragmaRX\Devices\Vendor\Laravel;
 
use PragmaRX\Devices\Devices;

use PragmaRX\Devices\Support\Config;
use PragmaRX\Devices\Support\Filesystem;

use PragmaRX\Devices\Deployers\Github;
use PragmaRX\Devices\Deployers\Bitbucket;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Foundation\AliasLoader as IlluminateAliasLoader;

class ServiceProvider extends IlluminateServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('pragmarx/devices', 'pragmarx/devices', __DIR__.'/../..');

        if( $this->getConfig('create_devices_alias') )
        {
            IlluminateAliasLoader::getInstance()->alias(
                                                            $this->getConfig('devices_alias'), 
                                                            'PragmaRX\Devices\Vendor\Laravel\Facade'
                                                        );
        }    
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFileSystem();

        $this->registerConfig();

        $this->registerDevices();

        $this->extendBlade();

        $this->disableViewCache();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Register the Filesystem driver used by Devices
     * 
     * @return void
     */
    private function registerFileSystem()
    {
        $this->app['devices.fileSystem'] = $this->app->share(function($app)
        {
            return new Filesystem;
        });
    }

    /**
     * Register the Config driver used by Devices
     * 
     * @return void
     */
    private function registerConfig()
    {
        $this->app['devices.config'] = $this->app->share(function($app)
        {
            return new Config($app['devices.fileSystem'], $app);
        });
    }

    /**
     * Takes all the components of Devices and glues them
     * together to create Devices.
     *
     * @return void
     */
    private function registerDevices()
    {
        $this->app['devices'] = $this->app->share(function($app)
        {
            $app['devices.loaded'] = true;

            return new Devices(
                                    $app['devices.config']
                                );
        });
    }

    private function extendBlade()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->extend(function ($view) 
        {
            return $this->app['devices']->process($view);
        });
    }

    private function disableViewCache()
    {
        if ($this->app['devices.config']->getLocalConfig('disable_view_cache'))
        {
            $this->app['devices.fileSystem']
                ->deleteDirectory($this->app['path.storage'].'/views', true);
        }
    }

    /**
     * Helper function to ease the use of configurations
     * 
     * @param  string $key configuration key
     * @return string      configuration value
     */
    public function getConfig($key)
    {
        return $this->app['config']["pragmarx/devices::$key"];
    }
}
