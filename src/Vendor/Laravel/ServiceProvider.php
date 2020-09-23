<?php

namespace PragmaRX\Tracker\Vendor\Laravel;

use PragmaRX\Support\GeoIp\GeoIp;
use PragmaRX\Support\PhpSession;
use PragmaRX\Support\ServiceProvider as PragmaRXServiceProvider;
use PragmaRX\Tracker\Data\Repositories\Agent;
use PragmaRX\Tracker\Data\Repositories\Connection;
use PragmaRX\Tracker\Data\Repositories\Cookie;
use PragmaRX\Tracker\Data\Repositories\Device;
use PragmaRX\Tracker\Data\Repositories\Domain;
use PragmaRX\Tracker\Data\Repositories\Error;
use PragmaRX\Tracker\Data\Repositories\Event;
use PragmaRX\Tracker\Data\Repositories\EventLog;
use PragmaRX\Tracker\Data\Repositories\GeoIp as GeoIpRepository;
use PragmaRX\Tracker\Data\Repositories\Language;
use PragmaRX\Tracker\Data\Repositories\Log;
use PragmaRX\Tracker\Data\Repositories\Path;
use PragmaRX\Tracker\Data\Repositories\Query;
use PragmaRX\Tracker\Data\Repositories\QueryArgument;
use PragmaRX\Tracker\Data\Repositories\Referer;
use PragmaRX\Tracker\Data\Repositories\Route;
use PragmaRX\Tracker\Data\Repositories\RoutePath;
use PragmaRX\Tracker\Data\Repositories\RoutePathParameter;
use PragmaRX\Tracker\Data\Repositories\Session;
use PragmaRX\Tracker\Data\Repositories\SqlQuery;
use PragmaRX\Tracker\Data\Repositories\SqlQueryBinding;
use PragmaRX\Tracker\Data\Repositories\SqlQueryBindingParameter;
use PragmaRX\Tracker\Data\Repositories\SqlQueryLog;
use PragmaRX\Tracker\Data\Repositories\SystemClass;
use PragmaRX\Tracker\Data\RepositoryManager;
use PragmaRX\Tracker\Eventing\EventStorage;
use PragmaRX\Tracker\Repositories\Message as MessageRepository;
use PragmaRX\Tracker\Services\Authentication;
use PragmaRX\Tracker\Support\Cache;
use PragmaRX\Tracker\Support\CrawlerDetector;
use PragmaRX\Tracker\Support\Exceptions\Handler as TrackerExceptionHandler;
use PragmaRX\Tracker\Support\LanguageDetect;
use PragmaRX\Tracker\Support\MobileDetect;
use PragmaRX\Tracker\Support\UserAgentParser;
use PragmaRX\Tracker\Tracker;
use PragmaRX\Tracker\Vendor\Laravel\Artisan\Tables as TablesCommand;
use PragmaRX\Tracker\Vendor\Laravel\Artisan\UpdateGeoIp;

class ServiceProvider extends PragmaRXServiceProvider
{
    protected $packageVendor = 'pragmarx';

    protected $packageName = 'tracker';

    protected $packageNameCapitalized = 'Tracker';

    protected $repositoryManagerIsBooted = false;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $userChecked = false;

    protected $tracker;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (!$this->getConfig('enabled')) {
            return false;
        }

        $this->loadRoutes();

        $this->registerErrorHandler();

        if (!$this->getConfig('use_middleware')) {
            $this->bootTracker();
        }

        $this->loadTranslations();
    }

    /**
     * Check if the service provider is full booted.
     *
     * @return void
     */
    public function isFullyBooted()
    {
        return $this->repositoryManagerIsBooted;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        if ($this->getConfig('enabled')) {
            $this->registerAuthentication();

            $this->registerCache();

            $this->registerRepositories();

            $this->registerTracker();

            $this->registerTablesCommand();

            $this->registerUpdateGeoIpCommand();

            $this->registerExecutionCallback();

            $this->registerUserCheckCallback();

            $this->registerSqlQueryLogWatcher();

            $this->registerGlobalEventLogger();

            $this->registerDatatables();

            $this->registerMessageRepository();

            $this->registerGlobalViewComposers();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['tracker'];
    }

    /**
     * Takes all the components of Tracker and glues them
     * together to create Tracker.
     *
     * @return void
     */
    protected function registerTracker()
    {
        $this->app->singleton('tracker', function ($app) {
            $app['tracker.loaded'] = true;

            return new Tracker(
                $app['tracker.config'],
                $app['tracker.repositories'],
                $app['request'],
                $app['router'],
                $app['log'],
                $app,
                $app['tracker.messages']
            );
        });
    }

    public function registerRepositories()
    {
        $this->app->singleton('tracker.repositories', function ($app) {
            try {
                $uaParser = new UserAgentParser($app->make('path.base'));
            } catch (\Exception $exception) {
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

            $refererSearchTermModel = $this->instantiateModel('referer_search_term_model');

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

            $languageModel = $this->instantiateModel('language_model');

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

            $crawlerDetect = new CrawlerDetector(
                $app['request']->headers->all(),
                $app['request']->server('HTTP_USER_AGENT')
            );

            $manager = new RepositoryManager(
                new GeoIp($this->getConfig('geoip_database_path')),
                new MobileDetect(),
                $uaParser,
                $app['tracker.authentication'],
                $app['session.store'],
                $app['tracker.config'],
                new Session(
                    $sessionModel,
                    $app['tracker.config'],
                    new PhpSession()
                ),
                $logRepository,
                new Path($pathModel),
                new Query($queryModel),
                new QueryArgument($queryArgumentModel),
                new Agent($agentModel),
                new Device($deviceModel),
                new Cookie(
                    $cookieModel,
                    $app['tracker.config'],
                    $app['request'],
                    $app['cookie']
                ),
                new Domain($domainModel),
                new Referer(
                    $refererModel,
                    $refererSearchTermModel,
                    $this->getAppUrl(),
                    $app->make('PragmaRX\Tracker\Support\RefererParser')
                ),
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
                $systemClassRepository,
                $crawlerDetect,
                new Language($languageModel),
                new LanguageDetect()
            );

            $this->repositoryManagerIsBooted = true;

            return $manager;
        });
    }

    public function registerAuthentication()
    {
        $this->app->singleton('tracker.authentication', function ($app) {
            return new Authentication($app['tracker.config'], $app);
        });
    }

    public function registerCache()
    {
        $this->app->singleton('tracker.cache', function ($app) {
            return new Cache($app['tracker.config'], $app);
        });
    }

    protected function registerTablesCommand()
    {
        $this->app->singleton('tracker.tables.command', function ($app) {
            return new TablesCommand();
        });

        $this->commands('tracker.tables.command');
    }

    protected function registerExecutionCallback()
    {
        $me = $this;

        $mathingEvents = [
            'router.matched',
            'Illuminate\Routing\Events\RouteMatched',
        ];

        $this->app['events']->listen($mathingEvents, function () use ($me) {
            $me->getTracker()->routerMatched($me->getConfig('log_routes'));
        });
    }

    protected function registerErrorHandler()
    {
        if ($this->getConfig('log_exceptions')) {
            $illuminateHandler = 'Illuminate\Contracts\Debug\ExceptionHandler';

            $handler = new TrackerExceptionHandler(
                $this->getTracker(),
                $this->app[$illuminateHandler]
            );

            // Replace original Illuminate Exception Handler by Tracker's
            $this->app[$illuminateHandler] = $handler;
        }
    }

    /**
     * @param string $modelName
     */
    protected function instantiateModel($modelName)
    {
        $model = $this->getConfig($modelName);

        if (!$model) {
            $message = "Tracker: Model not found for '$modelName'.";

            $this->app['log']->error($message);

            throw new \Exception($message);
        }

        $model = new $model();

        $model->setConfig($this->app['tracker.config']);

        if ($connection = $this->getConfig('connection')) {
            $model->setConnection($connection);
        }

        return $model;
    }

    protected function registerSqlQueryLogWatcher()
    {
        $me = $this;

        if (!class_exists('Illuminate\Database\Events\QueryExecuted')) {
            $this->app['events']->listen('illuminate.query', function (
                $query,
                $bindings,
                $time,
                $name
            ) use ($me) {
                $me->logSqlQuery($query, $bindings, $time, $name);
            });
        } else {
            $this->app['events']->listen('Illuminate\Database\Events\QueryExecuted', function ($query) use ($me) {
                $me->logSqlQuery($query);
            });
        }
    }

    /**
     * @param $query
     * @param $bindings
     * @param $time
     * @param $name
     * @param $me
     */
    public function logSqlQuery($query, $bindings = null, $time = null, $connectionName = null)
    {
        if ($this->getTracker()->isEnabled()) {
            if ($query instanceof \Illuminate\Database\Events\QueryExecuted) {
                $bindings = $query->bindings;
                $time = $query->time;
                $connectionName = $query->connectionName;
                $query = $query->sql;
            }

            $this->getTracker()->logSqlQuery($query, $bindings, $time, $connectionName);
        }
    }

    protected function registerGlobalEventLogger()
    {
        $me = $this;

        $this->app->singleton('tracker.events', function ($app) {
            return new EventStorage();
        });

        $this->app['events']->listen('*', function ($object = null) use ($me) {
            if ($me->app['tracker.events']->isOff() || !$me->isFullyBooted()) {
                return;
            }

            // To avoid infinite recursion, event tracking while logging events
            // must be turned off
            $me->app['tracker.events']->turnOff();

            // Log events even before application is ready
            // $me->app['tracker.events']->logEvent(
            //    $me->app['events']->firing(),
            //    $object
            // );
            // TODO: we have to investigate a way of doing this

            // Can only send events to database after application is ready
            if (isset($me->app['tracker.loaded'])) {
                $me->getTracker()->logEvents();
            }

            // Turn the event tracking to on again
            $me->app['tracker.events']->turnOn();
        });
    }

    protected function loadRoutes()
    {
        if (!$this->getConfig('stats_panel_enabled')) {
            return false;
        }

        $prefix = $this->getConfig('stats_base_uri');

        $namespace = $this->getConfig('stats_controllers_namespace');

        $filters = [];

        if ($before = $this->getConfig('stats_routes_before_filter')) {
            $filters['before'] = $before;
        }

        if ($after = $this->getConfig('stats_routes_after_filter')) {
            $filters['after'] = $after;
        }

        if ($middleware = $this->getConfig('stats_routes_middleware')) {
            $filters['middleware'] = $middleware;
        }

        $router = $this->app->make('router');

        $router->group(['namespace' => $namespace], function () use ($prefix, $router, $filters) {
            $router->group($filters, function () use ($prefix, $router) {
                $router->group(['prefix' => $prefix], function ($router) {
                    $router->get('/', ['as' => 'tracker.stats.index', 'uses' => 'Stats@index']);

                    $router->get('log/{uuid}', ['as' => 'tracker.stats.log', 'uses' => 'Stats@log']);

                    $router->get('api/pageviews', ['as' => 'tracker.stats.api.pageviews', 'uses' => 'Stats@apiPageviews']);

                    $router->get('api/pageviewsbycountry', ['as' => 'tracker.stats.api.pageviewsbycountry', 'uses' => 'Stats@apiPageviewsByCountry']);

                    $router->get('api/log/{uuid}', ['as' => 'tracker.stats.api.log', 'uses' => 'Stats@apiLog']);

                    $router->get('api/errors', ['as' => 'tracker.stats.api.errors', 'uses' => 'Stats@apiErrors']);

                    $router->get('api/events', ['as' => 'tracker.stats.api.events', 'uses' => 'Stats@apiEvents']);

                    $router->get('api/users', ['as' => 'tracker.stats.api.users', 'uses' => 'Stats@apiUsers']);

                    $router->get('api/visits', ['as' => 'tracker.stats.api.visits', 'uses' => 'Stats@apiVisits']);
                });
            });
        });
    }

    protected function registerDatatables()
    {
        $this->registerServiceProvider('Bllim\Datatables\DatatablesServiceProvider');

        $this->registerServiceAlias('Datatable', 'Bllim\Datatables\Facade\Datatables');
    }

    /**
     * Get the current package directory.
     *
     * @return string
     */
    public function getPackageDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..';
    }

    /**
     * Boot & Track.
     */
    protected function bootTracker()
    {
        $this->getTracker()->boot();
    }

    /**
     * Register global view composers.
     */
    protected function registerGlobalViewComposers()
    {
        $me = $this;

        $this->app->make('view')->composer('pragmarx/tracker::*', function ($view) use ($me) {
            $view->with('stats_layout', $me->getConfig('stats_layout'));

            $template_path = url('/').$me->getConfig('stats_template_path');

            $view->with('stats_template_path', $template_path);
        });
    }

    protected function registerUpdateGeoIpCommand()
    {
        $this->app->singleton('tracker.updategeoip.command', function ($app) {
            return new UpdateGeoIp();
        });

        $this->commands('tracker.updategeoip.command');
    }

    protected function registerUserCheckCallback()
    {
        $me = $this;

        $this->app['events']->listen('router.before', function ($object = null) use ($me) {

            // get auth bindings to check
            $bindings = $me->getConfig('authentication_ioc_binding');

            // check if all bindings are resolved
            $checked_bindings = array_map(function ($abstract) use ($me) {
                return $me->app->resolved($abstract);
            }, $bindings);

            $all_bindings_resolved =
                (!in_array(false, $checked_bindings, true)) ?: false;

            if ($me->tracker &&
                !$me->userChecked &&
                $me->getConfig('log_users') &&
                $all_bindings_resolved
            ) {
                $me->userChecked = $me->getTracker()->checkCurrentUser();
            }
        });
    }

    /**
     * @return Tracker
     */
    public function getTracker()
    {
        if (!$this->tracker) {
            $this->tracker = $this->app['tracker'];
        }

        return $this->tracker;
    }

    public function getRootDirectory()
    {
        return __DIR__.'/../..';
    }

    protected function getAppUrl()
    {
        return $this->app['request']->url();
    }

    public function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'tracker');
    }

    /**
     * Register the message repository.
     */
    protected function registerMessageRepository()
    {
        $this->app->singleton('tracker.messages', function () {
            return new MessageRepository();
        });
    }
}
