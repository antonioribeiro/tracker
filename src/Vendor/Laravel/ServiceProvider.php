<?php namespace PragmaRX\Tracker\Vendor\Laravel;

use PragmaRX\Tracker\Tracker;

use PragmaRX\Tracker\Services\Authentication;

use PragmaRX\Tracker\Support\Config;
use PragmaRX\Tracker\Support\MobileDetect;
use PragmaRX\Tracker\Support\UserAgentParser;

use PragmaRX\Tracker\Support\Database\Migrator as Migrator;

use PragmaRX\Tracker\Data\Repositories\Session;
use PragmaRX\Tracker\Data\Repositories\Log;
use PragmaRX\Tracker\Data\Repositories\Path;
use PragmaRX\Tracker\Data\Repositories\Query;
use PragmaRX\Tracker\Data\Repositories\QueryArgument;
use PragmaRX\Tracker\Data\Repositories\Agent;
use PragmaRX\Tracker\Data\Repositories\Device;
use PragmaRX\Tracker\Data\Repositories\Cookie;
use PragmaRX\Tracker\Data\Repositories\Domain;
use PragmaRX\Tracker\Data\Repositories\Referer;
use PragmaRX\Tracker\Data\Repositories\Route;
use PragmaRX\Tracker\Data\Repositories\RoutePath;
use PragmaRX\Tracker\Data\Repositories\RoutePathParameter;
use PragmaRX\Tracker\Data\Repositories\Error;

use PragmaRX\Tracker\Data\RepositoryManager;

use PragmaRX\Tracker\Vendor\Laravel\Artisan\Tables as TablesCommand;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Foundation\AliasLoader as IlluminateAliasLoader;

class ServiceProvider extends IlluminateServiceProvider {

    const PACKAGE_NAMESPACE = 'pragmarx/tracker';

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
        $this->package(self::PACKAGE_NAMESPACE, self::PACKAGE_NAMESPACE, __DIR__.'/../..');

        if( $this->app['config']->get(self::PACKAGE_NAMESPACE.'::create_tracker_alias') )
        {
            IlluminateAliasLoader::getInstance()->alias(
                                                            $this->getConfig('tracker_alias'),
                                                            'PragmaRX\Tracker\Vendor\Laravel\Facade'
                                                        );
        }

        $this->wakeUp();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Unfortunately, we are stuck with PHP session, because
        // Laravel's Session ID changes every time user logs in.
        session_start();

        new UserAgentParser($this->app->make('path.base'));

        $this->registerConfig();

        $this->registerAuthentication();

        $this->registerMigrator();

        $this->registerRepositories();

        $this->registerTracker();

	    $this->registerTablesCommand();

	    $this->registerExecutionCallBack();

	    $this->registerErrorHandler();

	    $this->commands('tracker.tables.command');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('tracker');
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
                                    $app['tracker.config'],
                                    $app['tracker.repositories'],
                                    $app['request'],
                                    $app['router'],
                                    $app['tracker.migrator']
                                );
        });
    }

    public function registerRepositories()
    {
        $this->app['tracker.repositories'] = $this->app->share(function($app)
        {
            $sessionModel = $this->instantiateModel('session_model');
            $logModel = $this->instantiateModel('log_model');
            $agentModel = $this->instantiateModel('agent_model');
            $deviceModel = $this->instantiateModel('device_model');
            $cookieModel = $this->instantiateModel('cookie_model');
	        $pathModel = $this->instantiateModel('path_model');
			$queryModel = $this->instantiateModel('query_model');
			$queryArgumentModel = $this->instantiateModel('query_argument_model');
	        $domainModel = $this->instantiateModel('domain_model');
	        $refererModel = $this->instantiateModel('referer_model');
	        $routeModel = $this->instantiateModel('route_model');
	        $routePathModel = $this->instantiateModel('route_path_model');
	        $routePathParameterModel = $this->instantiateModel('route_path_parameter_model');
	        $errorModel = $this->instantiateModel('error_model');

            return new RepositoryManager(
                                        new Session(new $sessionModel,
                                                    $app['tracker.config'],
                                                    $app['session.store']),

                                        new Log($logModel),

                                        new Path($pathModel),

                                        new Query($queryModel),

                                        new QueryArgument($queryArgumentModel),

                                        new Agent($agentModel),

                                        new Device($deviceModel),

                                        new Cookie($cookieModel,
                                                    $app['tracker.config'],
                                                    $app['request'],
                                                    $app['cookie']),

                                        new Domain($domainModel),

                                        new Referer($refererModel),

                                        new Route($routeModel),

                                        new RoutePath($routePathModel),

                                        new RoutePathParameter($routePathParameterModel),

                                        new Error($errorModel),

                                        new MobileDetect,

                                        new UserAgentParser($app->make('path.base')),

                                        $app['tracker.authentication'],

                                        $app['session.store'],

                                        $app['tracker.config']
                                    );
        });
    }

    public function registerAuthentication()
    {
        $this->app['tracker.authentication'] = $this->app->share(function($app)
        {
            return new Authentication($app['tracker.config'], $app);
        });
    }

    public function registerConfig()
    {
        $this->app['tracker.config'] = $this->app->share(function($app)
        {
            return new Config($app['config'], self::PACKAGE_NAMESPACE);
        });
    }

    public function registerMigrator()
    {
        $this->app['tracker.migrator'] = $this->app->share(function($app)
        {
            $connection = $this->getConfig('connection');

            return new Migrator($app['db']->connection($connection)->getSchemaBuilder());
        });
    }

    private function wakeUp()
    {
        $this->app['tracker']->boot();
    }

    private function getConfig($key)
    {
        return $this->app['config']->get(self::PACKAGE_NAMESPACE.'::'.$key);
    }

	private function registerTablesCommand()
	{
		$this->app['tracker.tables.command'] = $this->app->share(function($app)
		{
			return new TablesCommand();
		});
	}

	private function registerExecutionCallBack()
	{
		$me = $this;

		$this->app['events']->listen('router.matched', function() use ($me)
		{
			$me->app['tracker']->routerMatched();
		});
	}

	private function registerErrorHandler()
	{
		$me = $this;

		$this->app->error(function(\Exception $exception, $code) use ($me)
		{
			$me->app['tracker']->handleException($exception, $code);
		});
	}

	private function instantiateModel($modelName)
	{
		$model = $this->getConfig($modelName);

		if ( ! $model)
		{
			$message = "Tracker: Model not found for '$modelName'.";

			$this->app['log']->error($message);

			throw new \Exception($message);
		}

		return new $model;
	}

}