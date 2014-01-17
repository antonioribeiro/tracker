<?php namespace PragmaRX\Steroids\Vendor\Laravel;
 
use PragmaRX\Steroids\Steroids;

use PragmaRX\Steroids\Support\Config;
use PragmaRX\Steroids\Support\Filesystem;

use PragmaRX\Steroids\Deployers\Github;
use PragmaRX\Steroids\Deployers\Bitbucket;

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
        $this->package('pragmarx/steroids', 'pragmarx/steroids', __DIR__.'/../..');

        if( $this->getConfig('create_steroids_alias') )
        {
            IlluminateAliasLoader::getInstance()->alias(
                                                            $this->getConfig('steroids_alias'), 
                                                            'PragmaRX\Steroids\Vendor\Laravel\Facade'
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

        $this->registerSteroids();

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
     * Register the Filesystem driver used by Steroids
     * 
     * @return void
     */
    private function registerFileSystem()
    {
        $this->app['steroids.fileSystem'] = $this->app->share(function($app)
        {
            return new Filesystem;
        });
    }

    /**
     * Register the Config driver used by Steroids
     * 
     * @return void
     */
    private function registerConfig()
    {
        $this->app['steroids.config'] = $this->app->share(function($app)
        {
            return new Config($app['steroids.fileSystem'], $app);
        });
    }

    /**
     * Takes all the components of Steroids and glues them
     * together to create Steroids.
     *
     * @return void
     */
    private function registerSteroids()
    {
        $this->app['steroids'] = $this->app->share(function($app)
        {
            $app['steroids.loaded'] = true;

            return new Steroids(
                                    $app['steroids.config']
                                );
        });
    }

    private function extendBlade()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->extend(function ($view) 
        {
            return $this->app['steroids']->process($view);
        });
    }

    private function disableViewCache()
    {
        if ($this->app['steroids.config']->getLocalConfig('disable_view_cache'))
        {
            $this->app['steroids.fileSystem']
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
        return $this->app['config']["pragmarx/steroids::$key"];
    }
}
