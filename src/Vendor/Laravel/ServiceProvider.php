<?php namespace PragmaRX\Tracker\Vendor\Laravel;

use PragmaRX\Tracker\Data\Repositories\Connection;
use PragmaRX\Tracker\Data\Repositories\Event;
use PragmaRX\Tracker\Data\Repositories\EventLog;
use PragmaRX\Tracker\Data\Repositories\SqlQueryBindingParameter;
use PragmaRX\Tracker\Data\Repositories\SystemClass;
use PragmaRX\Tracker\Eventing\EventStorage;
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
use PragmaRX\Tracker\Data\Repositories\GeoIp as GeoIpRepository;
use PragmaRX\Tracker\Data\Repositories\SqlQuery;
use PragmaRX\Tracker\Data\Repositories\SqlQueryLog;
use PragmaRX\Tracker\Data\Repositories\SqlQueryBinding;

use PragmaRX\Tracker\Data\RepositoryManager;

use PragmaRX\Tracker\Vendor\Laravel\Artisan\Tables as TablesCommand;
use PragmaRX\Tracker\Vendor\Laravel\Artisan\UpdateParser as UpdateParserCommand;

use PragmaRX\Support\GeoIp;

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
	    if ($this->getConfig('enabled'))
	    {
		    $this->package(self::PACKAGE_NAMESPACE, self::PACKAGE_NAMESPACE, __DIR__.'/../..');

		    if( $this->app['config']->get(self::PACKAGE_NAMESPACE.'::create_tracker_alias') )
		    {
			    IlluminateAliasLoader::getInstance()->alias(
				    $this->getConfig('tracker_alias'),
				    'PragmaRX\Tracker\Vendor\Laravel\Facade'
			    );
		    }

		    $this->loadRoutes();

		    $this->registerErrorHandler();

		    $this->wakeUp();
	    }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
	    $this->registerConfig();

	    if ($this->getConfig('enabled'))
	    {
		    $this->registerAuthentication();

		    $this->registerMigrator();

		    $this->registerRepositories();

		    $this->registerTracker();

		    $this->registerTablesCommand();

		    $this->registerUpdateParserCommand();

		    $this->registerExecutionCallBack();

		    $this->registerSqlQueryLogWatcher();

		    $this->registerGlobalEventLogger();

		    $this->commands('tracker.tables.command');

		    $this->commands('tracker.updateparser.command');
	    }
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
                                    $app['tracker.migrator'],
                                    $app['log'],
                                    $app
                                );
        });
    }

    public function registerRepositories()
    {
        $this->app['tracker.repositories'] = $this->app->share(function($app)
        {
            try
            {
                $uaParser = new UserAgentParser($app->make('path.base'));
            }
            catch (\Exception $exception)
            {
                $uaParser = null;
            }

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

	        $geoipModel = $this->instantiateModel('geoip_model');

	        $sqlQueryModel = $this->instantiateModel('sql_query_model');

            $sqlQueryBindingModel = $this->instantiateModel('sql_query_binding_model');

	        $sqlQueryBindingParameterModel = $this->instantiateModel('sql_query_binding_parameter_model');

            $sqlQueryLogModel = $this->instantiateModel('sql_query_log_model');

	        $connectionModel = $this->instantiateModel('connection_model');

	        $eventModel = $this->instantiateModel('event_model');

	        $eventLogModel = $this->instantiateModel('event_log_model');

	        $systemClassModel = $this->instantiateModel('system_class_model');

	        $logRepository = new Log($logModel);

	        $connectionRepository = new Connection($connectionModel);

	        $sqlQueryBindingRepository = new SqlQueryBinding($sqlQueryBindingModel);

	        $sqlQueryBindingParameterRepository = new SqlQueryBindingParameter($sqlQueryBindingParameterModel);

	        $sqlQueryLogRepository = new SqlQueryLog($sqlQueryLogModel);

	        $sqlQueryRepository = new SqlQuery(
		        $sqlQueryModel,
		        $sqlQueryLogRepository,
		        $sqlQueryBindingRepository,
		        $sqlQueryBindingParameterRepository,
		        $connectionRepository,
		        $logRepository,
		        $app['tracker.config']
	        );

			$eventLogRepository = new EventLog($eventLogModel);

			$systemClassRepository = new SystemClass($systemClassModel);

	        $eventRepository = new Event(
		        $eventModel,
		        $app['tracker.events'],
		        $eventLogRepository,
		        $systemClassRepository,
		        $logRepository,
		        $app['tracker.config']
	        );

	        $routeRepository = new Route(
		        $routeModel,
		        $app['tracker.config']
	        );

	        return new RepositoryManager(
	            new GeoIp(),

	            new MobileDetect,

	            $uaParser,

	            $app['tracker.authentication'],

	            $app['session.store'],

	            $app['tracker.config'],

                new Session($sessionModel,
                            $app['tracker.config'],
                            $app['session.store']),

                $logRepository,

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

                $routeRepository,

                new RoutePath($routePathModel),

                new RoutePathParameter($routePathParameterModel),

                new Error($errorModel),

                new GeoIpRepository($geoipModel),

				$sqlQueryRepository,

                $sqlQueryBindingRepository,

                $sqlQueryBindingParameterRepository,

                $sqlQueryLogRepository,

	            $connectionRepository,

	            $eventRepository,

	            $eventLogRepository,

	            $systemClassRepository
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

            return new Migrator($app['db'], $connection);
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

    private function registerUpdateParserCommand()
    {
        $this->app['tracker.updateparser.command'] = $this->app->share(function($app)
        {
            return new UpdateParserCommand(
	            $app['tracker.config']
            );
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
		if ($this->getConfig('log_exceptions'))
		{
			$me = $this;

			$this->app->error(function(\Exception $exception, $code) use ($me)
			{
				$me->app['tracker']->handleException($exception, $code);
			});
		}
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

        $model = new $model;

        $model->config = $this->app['tracker.config'];

        if ($connection = $this->getConfig('connection'))
        {
            $model->setConnection($connection);
        }

		return $model;
	}

	private function registerSqlQueryLogWatcher()
	{
		$me = $this;

		$this->app['events']->listen('illuminate.query', function($query,
		                                                          $bindings,
		                                                          $time,
		                                                          $name) use ($me)
		{
			$me->app['tracker']->logSqlQuery(
				$query, $bindings, $time, $name
			);
		});
	}

	private function registerGlobalEventLogger()
	{
		$me = $this;

		$this->app['tracker.events'] = $this->app->share(function($app)
		{
			return new EventStorage();
		});

		$this->app['events']->listen('*', function($object = null) use ($me)
		{
			if ($me->app['tracker.events']->isOff())
			{
				return;
			}

			// To avoid infinite recursion, event tracking while logging events
			// must be turned off
			$me->app['tracker.events']->turnOff();

			// Log events even before application is ready
			$me->app['tracker.events']->logEvent(
				$me->app['events']->firing(),
				$object
			);

			// Can only send events to database after application is ready
			if (isset($me->app['tracker.loaded']))
			{
				$me->app['tracker']->logEvents();
			}

			// Turn the event tracking to on again
			$me->app['tracker.events']->turnOn();
		});

	}

	private function loadRoutes()
	{
		if ($this->app['config']->get(self::PACKAGE_NAMESPACE.'::stats_panel_enabled'))
		{
			include __DIR__.'/../../Vendor/Laravel/App/routes.php';
		}
	}

}
