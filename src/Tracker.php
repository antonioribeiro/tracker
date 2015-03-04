<?php

namespace PragmaRX\Tracker;

use PragmaRX\Support\Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Log\Writer as Logger;
use PragmaRX\Tracker\Support\Minutes;
use Illuminate\Foundation\Application as Laravel;
use PragmaRX\Tracker\Support\Database\Migrator as Migrator;
use PragmaRX\Tracker\Data\RepositoryManager as DataRepositoryManager;

class Tracker
{
    private $config;

	/**
	 * @var \Illuminate\Routing\Router
	 */
	private $route;

    private $logger;
	/**
	 * @var \Illuminate\Foundation\Application
	 */
	private $laravel;

	public function __construct(
                                    Config $config,
                                    DataRepositoryManager $dataRepositoryManager,
                                    Request $request,
                                    Router $route,
                                    Migrator $migrator,
                                    Logger $logger,
								    Laravel $laravel
                                )
    {
        $this->config = $config;

        $this->dataRepositoryManager = $dataRepositoryManager;

        $this->request = $request;

        $this->migrator = $migrator;

	    $this->route = $route;

        $this->logger = $logger;

	    $this->laravel = $laravel;
    }

    public function boot()
    {
        if ($this->isTrackable())
        {
            $this->track();
        }
    }

    public function track()
    {
        $log = $this->getLogData();

        if ($this->config->get('log_enabled'))
        {
            $this->dataRepositoryManager->createLog($log);
        }
    }

	/**
	 * @return array
	 */
	private function getSessionData()
	{
		return array(
			'user_id' => $this->getUserId(),
			'device_id' => $this->getDeviceId(),
			'client_ip' => $this->request->getClientIp(),
			'geoip_id' => $this->getGeoIpId(),
			'agent_id' => $this->getAgentId(),
			'referer_id' => $this->getRefererId(),
			'cookie_id' => $this->getCookieId(),
			'is_robot' => $this->isRobot(),

			// The key user_agent is not present in the sessions table, but
			// it's internally used to check if the user agent changed
			// during a session.
			'user_agent' => $this->dataRepositoryManager->getCurrentUserAgent(),
		);
	}

	/**
	 * @return array
	 */
	private function getLogData()
	{
		return array(
			'session_id' => $this->getSessionId(true),
			'method' => $this->request->method(),
			'path_id' => $this->getPathId(),
			'query_id' => $this->getQueryId(),
			'is_ajax' => $this->request->ajax(),
			'is_secure' => $this->request->isSecure(),
			'is_json' => $this->request->isJson(),
			'wants_json' => $this->request->wantsJson(),
		);
	}

    public function getSessionId($updateLastActivity = false)
    {
        return $this->dataRepositoryManager->getSessionId(
            $this->getSessionData(),
            $updateLastActivity
        );
    }

    public function getUserId()
    {
	    return $this->config->get('log_users')
			    ? $this->dataRepositoryManager->getCurrentUserId()
			    : null;
    }

    public function getCookieId()
    {
	    return $this->config->get('store_cookie_tracker')
		        ? $this->dataRepositoryManager->getCookieId()
		        : null;
    }

    public function getDeviceId()
    {
	    return $this->config->get('log_devices')
		    ?   $this->dataRepositoryManager->findOrCreateDevice(
			        $this->dataRepositoryManager->getCurrentDeviceProperties()
		        )
		    : null;
    }

	public function getPathId()
	{
		return $this->config->get('log_paths')
			?   $this->dataRepositoryManager->findOrCreatePath(
					array(
						'path' => $this->request->path()
					)
				)
			: null;
	}

	public function getQueryId()
	{
		if ($this->config->get('log_queries'))
		{
			if (count($arguments = $this->request->query()))
			{
				return $this->dataRepositoryManager->getQueryId(
					array(
						'query' => array_implode('=', '|', $arguments),
						'arguments' => $arguments
					)
				);
			}
		}
	}

    public function getMigrator()
    {
        return $this->migrator;
    }

	public function routerMatched()
	{
		if ($this->config->get('enabled') && $this->config->get('log_routes'))
		{
		    if ($this->dataRepositoryManager->routeIsTrackable($this->route))
		    {
			    $this->dataRepositoryManager->updateRoute(
				    $this->getRoutePathId($this->route->current())
			    );
		    }
			else
			{
				$this->deleteCurrentLog();
			}
		}
	}

	private function getRefererId()
	{
		return $this->config->get('log_referers')
				?   $this->dataRepositoryManager->getRefererId(
						$this->request->headers->get('referer')
					)
				: null;
	}

	public function getDomainId($domain)
	{
		return $this->dataRepositoryManager->getDomainId($domain);
	}

	private function getRoutePathId()
	{
		return $this->dataRepositoryManager->getRoutePathId($this->route, $this->request);
	}

	public function handleException($exception, $code)
	{
		if ($this->config->get('log_enabled'))
		{
			try
			{
				return $this->dataRepositoryManager->handleException($exception, $code);
			}
			catch (\Exception $e) {}
		}
	}

    public function allSessions()
    {
        return $this->dataRepositoryManager->getAllSessions();
    }

    public function parserIsAvailable()
    {
        if ( ! $this->dataRepositoryManager->parserIsAvailable() )
        {
            $this->logger->error('Tracker: uaparser regex file not available. "Execute php artisan tracker:updateparser" to generate it.');

            return false;
        }

        return true;
    }

	private function isTrackableIp()
	{
		return ! ipv4_in_range(
			$this->request->getClientIp(),
			$this->config->get('do_not_track_ips')
		);
	}

	private function isTrackableEnvironment()
	{
		return ! in_array(
			$this->laravel->environment(),
			$this->config->get('do_not_track_environments')
		);
	}

	public function logSqlQuery($query, $bindings, $time, $name)
	{
		if (
			$this->isTrackable() &&
			$this->config->get('log_enabled') &&
			$this->config->get('log_sql_queries') &&
			$this->isSqlQueriesLoggableConnection($name)
		)
		{
			$this->dataRepositoryManager->logSqlQuery($query, $bindings, $time, $name);
		}
	}

	private function isSqlQueriesLoggableConnection($name)
	{
		return ! in_array(
			$name,
			$this->config->get('do_not_log_sql_queries_connections')
		);
	}

	private function isTrackable()
	{
		return $this->config->get('enabled') &&
				$this->logIsEnabled() &&
				$this->parserIsAvailable() &&
				$this->isTrackableIp() &&
				$this->isTrackableEnvironment() &&
				$this->notRobotOrTrackable();
	}

	public function logEvents()
	{
		if (
			$this->isTrackable() &&
			$this->config->get('log_enabled') &&
			$this->config->get('log_events')
		)
		{
			$this->dataRepositoryManager->logEvents();
		}
	}

    public function sessions($minutes = 1440, $results = true)
    {
        return $this->dataRepositoryManager->getLastSessions(Minutes::make($minutes), $results);
    }

	public function sessionLog($uuid, $results = true)
	{
		return $this->dataRepositoryManager->getSessionLog($uuid, $results);
	}

	public function pageViews($minutes, $results = true)
    {
    	return $this->dataRepositoryManager->pageViews(Minutes::make($minutes), $results);
    }

    public function pageViewsByCountry($minutes, $results = true)
    {
    	return $this->dataRepositoryManager->pageViewsByCountry(Minutes::make($minutes), $results);
    }

    public function users($minutes, $results = true)
    {
    	return $this->dataRepositoryManager->users(Minutes::make($minutes), $results);
    }

	public function events($minutes, $results = true)
	{
		return $this->dataRepositoryManager->events(Minutes::make($minutes), $results);
	}

	public function errors($minutes, $results = true)
	{
		return $this->dataRepositoryManager->errors(Minutes::make($minutes), $results);
	}

	public function currentSession()
	{
		return $this->dataRepositoryManager->sessionRepository->getCurrent();
	}

	public function isRobot()
	{
		return $this->dataRepositoryManager->isRobot();
	}

	private function notRobotOrTrackable()
	{
		return
			! $this->isRobot() ||
			! $this->config->get('do_not_track_robots');
	}

	private function getGeoIpId()
	{
		return $this->config->get('log_geoip')
				? $this->dataRepositoryManager->getGeoIpId($this->request->getClientIp())
				: null;
	}

	private function getAgentId()
	{
		return $this->config->get('log_user_agents')
				? $this->dataRepositoryManager->getAgentId()
				: null;
	}

	public function logByRouteName($name, $minutes = null)
	{
		if ($minutes)
		{
			$minutes = Minutes::make($minutes);
		}

		return $this->dataRepositoryManager->logByRouteName($name, $minutes);
	}

	public function getConfig($key)
	{
		return $this->config->get($key);
	}

	private function deleteCurrentLog()
	{
		$this->dataRepositoryManager->logRepository->delete();
	}

	private function logIsEnabled()
	{
		return
			$this->config->get('log_enabled') ||
			$this->config->get('log_sql_queries') ||
			$this->config->get('log_sql_queries_bindings') ||
			$this->config->get('log_events') ||
			$this->config->get('log_geoip') ||
			$this->config->get('log_user_agents') ||
			$this->config->get('log_users') ||
			$this->config->get('log_devices') ||
			$this->config->get('log_referers') ||
			$this->config->get('log_paths') ||
			$this->config->get('log_queries') ||
			$this->config->get('log_routes') ||
			$this->config->get('log_exceptions');
	}

}
