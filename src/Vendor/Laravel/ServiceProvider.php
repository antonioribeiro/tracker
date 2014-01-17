<?php namespace PragmaRX\Tracker\Vendor\Laravel;
 
use PragmaRX\Tracker\Tracker;

use PragmaRX\Tracker\Support\Config;
use PragmaRX\Tracker\Support\Filesystem;

use PragmaRX\Tracker\Deployers\Github;
use PragmaRX\Tracker\Deployers\Bitbucket;

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
        $this->package('pragmarx/tracker', 'pragmarx/tracker', __DIR__.'/../..');

        if( $this->getConfig('create_tracker_alias') )
        {
            IlluminateAliasLoader::getInstance()->alias(
                                                            $this->getConfig('tracker_alias'), 
                                                            'PragmaRX\Tracker\Vendor\Laravel\Facade'
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

        $this->registerTracker();

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
     * Register the Filesystem driver used by Tracker
     * 
     * @return void
     */
    private function registerFileSystem()
    {
        $this->app['tracker.fileSystem'] = $this->app->share(function($app)
        {
            return new Filesystem;
        });
    }

    /**
     * Register the Config driver used by Tracker
     * 
     * @return void
     */
    private function registerConfig()
    {
        $this->app['tracker.config'] = $this->app->share(function($app)
        {
            return new Config($app['tracker.fileSystem'], $app);
        });
    }

    /**
     * Takes all the components of Tracker and glues them
     * together to create Tracker.
     *
     * @return void
     */
    private function registerTracker()
    {
        $this->app['tracker'] = $this->app->share(function($app)
        {
            $app['tracker.loaded'] = true;

            return new Tracker(
                                    $app['tracker.config']
                                );
        });
    }

    private function extendBlade()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->extend(function ($view) 
        {
            return $this->app['tracker']->process($view);
        });
    }

    private function disableViewCache()
    {
        if ($this->app['tracker.config']->getLocalConfig('disable_view_cache'))
        {
            $this->app['tracker.fileSystem']
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
        return $this->app['config']["pragmarx/tracker::$key"];
    }
}
