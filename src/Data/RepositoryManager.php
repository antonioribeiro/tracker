<?php

/**
 * Part of the Tracker package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Tracker
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data;

use PragmaRX\Tracker\Data\Repositories\Connection;
use PragmaRX\Tracker\Data\Repositories\Event;
use PragmaRX\Tracker\Data\Repositories\EventLog;
use PragmaRX\Tracker\Data\Repositories\SqlQuery;
use PragmaRX\Tracker\Data\Repositories\SqlQueryBinding;
use PragmaRX\Tracker\Data\Repositories\SqlQueryBindingParameter;
use PragmaRX\Tracker\Data\Repositories\SqlQueryLog;
use PragmaRX\Tracker\Data\Repositories\SystemClass;
use PragmaRX\Tracker\Support\MobileDetect;
use PragmaRX\Support\Config;

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

use PragmaRX\Tracker\Services\Authentication;

use PragmaRX\Support\GeoIp;

use Illuminate\Session\Store as IlluminateSession;

class RepositoryManager implements RepositoryManagerInterface {

	/**
	 * @var Path
	 */
	private $pathRepository;

	/**
	 * @var Query
	 */

	private $queryRepository;
	/**
	 * @var QueryArgument
	 */

	private $queryArgumentRepository;
	/**
	 * @var Domain
	 */
	private $domainRepository;
	/**
	 * @var Referer
	 */
	private $refererRepository;
	/**
	 * @var Repositories\Route
	 */
	private $routeRepository;
	/**
	 * @var Repositories\RoutePath
	 */
	private $routePathRepository;
	/**
	 * @var Repositories\RoutePathParameter
	 */
	private $routePathParameterRepository;
	/**
	 * @var Error
	 */
	private $errorRepository;
	/**
	 * @var GeoIP
	 */
	private $geoIp;

	private $geoIpRepository;

	/**
	 * @var Repositories\SqlQuery
	 */
	private $sqlQueryRepository;

	/**
	 * @var Repositories\SqlQueryBinding
	 */
	private $sqlQueryBindingRepository;

	/**
	 * @var Repositories\SqlQueryLog
	 */
	private $sqlQueryLogRepository;

	private $sqlQueryBindingParameterRepository;

	/**
	 * @var Repositories\Connection
	 */
	private $connectionRepository;

	/**
	 * @var Repositories\Event
	 */
	private $eventRepository;

	/**
	 * @var Repositories\EventLog
	 */
	private $eventLogRepository;

	/**
	 * @var Repositories\SystemClass
	 */
	private $systemClassRepository;

	public function __construct(
		GeoIP $geoIp,
		MobileDetect $mobileDetect,
		$userAgentParser,
		Authentication $authentication,
		IlluminateSession $session,
		Config $config,
        Session $sessionRepository,
        Log $logRepository,
		Path $pathRepository,
		Query $queryRepository,
		QueryArgument $queryArgumentRepository,
        Agent $agentRepository,
        Device $deviceRepository,
        Cookie $cookieRepository,
        Domain $domainRepository,
        Referer $refererRepository,
        Route $routeRepository,
        RoutePath $routePathRepository,
        RoutePathParameter $routePathParameterRepository,
        Error $errorRepository,
        GeoIpRepository $geoIpRepository,
        SqlQuery $sqlQueryRepository,
        SqlQueryBinding $sqlQueryBindingRepository,
        SqlQueryBindingParameter $sqlQueryBindingParameterRepository,
        SqlQueryLog $sqlQueryLogRepository,
		Connection $connectionRepository,
		Event $eventRepository,
		EventLog $eventLogRepository,
		SystemClass $systemClassRepository
    )
    {
	    $this->authentication = $authentication;

	    $this->mobileDetect = $mobileDetect;

	    $this->userAgentParser = $userAgentParser;

	    $this->session = $session;

	    $this->config = $config;

	    $this->geoIp = $geoIp;

        $this->sessionRepository = $sessionRepository;

        $this->logRepository = $logRepository;

	    $this->pathRepository = $pathRepository;

	    $this->queryRepository = $queryRepository;

	    $this->queryArgumentRepository = $queryArgumentRepository;

        $this->agentRepository = $agentRepository;

        $this->deviceRepository = $deviceRepository;

        $this->cookieRepository = $cookieRepository;

	    $this->domainRepository = $domainRepository;

	    $this->refererRepository = $refererRepository;

	    $this->routeRepository = $routeRepository;

	    $this->routePathRepository = $routePathRepository;

	    $this->routePathParameterRepository = $routePathParameterRepository;

	    $this->errorRepository = $errorRepository;

	    $this->geoIpRepository = $geoIpRepository;

	    $this->sqlQueryRepository = $sqlQueryRepository;

	    $this->sqlQueryBindingRepository = $sqlQueryBindingRepository;

	    $this->sqlQueryBindingParameterRepository = $sqlQueryBindingParameterRepository;

	    $this->sqlQueryLogRepository = $sqlQueryLogRepository;

	    $this->connectionRepository = $connectionRepository;

	    $this->eventRepository = $eventRepository;

	    $this->eventLogRepository = $eventLogRepository;

	    $this->systemClassRepository = $systemClassRepository;
    }

    public function createLog($data)
    {
	    $this->logRepository->createLog($data);

	    $this->sqlQueryRepository->fire();
    }

    public function findOrCreateSession($data)
    {
        return $this->sessionRepository->findOrCreate($data, array('uuid'));
    }

	public function findOrCreatePath($path)
	{
		return $this->pathRepository->findOrCreate($path, array('path'));
	}

    public function findOrCreateAgent($data)
    {
        return $this->agentRepository->findOrCreate($data, array('name'));
    }

    public function findOrCreateDevice($data)
    {
        return $this->deviceRepository->findOrCreate($data, array('kind', 'model', 'platform', 'platform_version'));
    }

    public function getAgentId()
    {
        return $this->findOrCreateAgent($this->getCurrentAgentArray());
    }

    public function getCurrentUserAgent()
    {
        return $this->userAgentParser->originalUserAgent;
    }

    public function getCurrentAgentArray()
    {
        return array(
                        'name' => $this->getCurrentUserAgent() ?: 'Other',

                        'browser' => $this->userAgentParser->userAgent->family,

                        'browser_version' => $this->userAgentParser->getUserAgentVersion(),
                    );
    }

    public function getCurrentDeviceProperties()
    {
        $properties = $this->mobileDetect->detectDevice();

        $properties['platform'] = $this->userAgentParser->operatingSystem->family;

        $properties['platform_version'] = $this->userAgentParser->getOperatingSystemVersion();

        return $properties;
    }

    public function getCurrentUserId()
    {
        return $this->authentication->getCurrentUserId();
    }

    public function getSessionId($sessionInfo, $updateLastActivity)
    {
        return $this->sessionRepository->getCurrentId($sessionInfo, $updateLastActivity);
    }

    public function getCookieId()
    {
        return $this->cookieRepository->getId();
    }

	public function getQueryId($query)
	{
		if ( ! $query)
		{
			return;
		}

		return $this->findOrCreateQuery($query);
	}

	public function findOrCreateQuery($data)
	{
		$id = $this->queryRepository->findOrCreate($data, array('query'), $created);

		if ($created)
		{
			foreach ($data['arguments'] as $argument => $value)
			{
				if (is_array($value))
				{
					$value = multi_implode(',', $value);
				}

				$this->queryArgumentRepository->create(
					array(
						'query_id' => $id,
						'argument' => $argument,
						'value' => $value,
					)
				);
			}
		}

		return $id;
	}

	public function updateRoute($route_id)
	{
		return $this->logRepository->updateRoute($route_id);
	}

	public function getDomainId($domain)
	{
		return $this->domainRepository->findOrCreate(
			array('name' => $domain),
			array('name')
		);
	}

	public function getRefererId($referer)
	{
		if ($referer)
		{
			$url = parse_url($referer);

			$parts = explode(".", $url['host']);

			$domain = array_pop($parts);
			if(sizeof($parts) > 0){
				$domain = array_pop($parts) . "." . $domain;
			}

			$domain_id = $this->getDomainId($domain);

			return $this->refererRepository->findOrCreate(
				array(
					'url' => $referer,
					'host' => $url['host'],
					'domain_id' => $domain_id,
				),
				array('url')
			);
		}
	}

	public function getRoutePathId($route, $request)
	{
		$route_id = $this->getRouteId(
			$route->currentRouteName() ?: '',
			$route->currentRouteAction() ?: 'closure'
		);

		$created = false;

		$route_path_id = $this->getRoutePath(
			$route_id,
			$request->path(),
			$created
		);

		if ($created)
		{
			foreach ($route->current()->parameters() as $parameter => $value)
			{
				// When the parameter value is a whole model, we have
				// two options left:
				//
				//  1) Return model id, if it's available as 'id'
				//  2) Return null (not ideal, but, what could we do?)
				//
				// Should we store the whole model? Not really useful, right?

				if ($value instanceof \Illuminate\Database\Eloquent\Model)
				{
					$model_id = null;

					foreach ($this->config->get('id_columns_names', ['id']) as $column)
					{
						if (property_exists($value, $column))
						{
							$model_id = $value->$column;

							break;
						}
					}

					$value = $model_id;
				}

				if ($route_path_id && $parameter && $value)
				{
					$this->createRoutePathParameter($route_path_id, $parameter, $value);
				}
			}
		}

		return $route_path_id;
	}

	private function getRouteId($name, $action)
	{
		return $this->routeRepository->findOrCreate(
			array('name' => $name, 'action' => $action),
			array('name', 'action')
		);
	}

	private function getRoutePath($route_id, $path, &$created = null)
	{
		return $this->routePathRepository->findOrCreate(
			array('route_id' => $route_id, 'path' => $path),
			array('route_id', 'path'),
			$created
		);
	}

	private function createRoutePathParameter($route_path_id, $parameter, $value)
	{
		return $this->routePathParameterRepository->create(
			array(
				'route_path_id' => $route_path_id,
				'parameter' => $parameter,
				'value' => $value,
			)
		);
	}

	public function handleException($exception, $code)
	{
		$error_id = $this->errorRepository->findOrCreate(
			array('message' => $exception->getMessage(), 'code' => $code),
			array('message', 'code')
		);

		$this->logRepository->updateError($error_id);
	}

	public function getLastSessions($minutes, $results)
	{
		return $this->sessionRepository->last($minutes, $results);
	}

	public function getAllSessions()
	{
		return $this->sessionRepository->all();
	}

	public function parserIsAvailable()
	{
		return ! empty($this->userAgentParser);
	}

	public function getGeoIpId($clientIp)
	{
		$id = null;

		if($geoip = $this->geoIp->byAddr($clientIp))
		{
			$id = $this->geoIpRepository->findOrCreate(
				$this->geoIp->byAddr($clientIp),
				array('latitude', 'longitude')
			);
		}

		return $id;
	}

	public function getSessionLog($uuid, $results = true)
	{
		$session = $this->sessionRepository->findByUuid($uuid);

		return $this->logRepository->bySession($session->id, $results);
	}

	public function pageViews($minutes, $results)
	{
		return $this->logRepository->pageViews($minutes, $results);
	}

	public function pageViewsByCountry($minutes, $results)
	{
		return $this->logRepository->pageViewsByCountry($minutes, $results);
	}

	public function logSqlQuery($query, $bindings, $time, $name)
	{
		$this->sqlQueryRepository->push(array(
			'query' => $query,
			'bindings' => $bindings,
			'time' => $time,
			'name' => $name,
		));
	}

	public function logEvents()
	{
		$this->eventRepository->logEvents();
	}

	public function users($minutes, $results)
	{
		return $this->sessionRepository->users($minutes, $results);
	}

	public function events($minutes, $results)
	{
		return $this->eventRepository->getAll($minutes, $results);
	}

	public function errors($minutes, $results)
	{
		return $this->logRepository->getErrors($minutes, $results);
	}

	public function isRobot()
	{
		return $this->mobileDetect->isRobot();
	}

	public function logByRouteName($name, $minutes = null)
	{
		return $this->logRepository->allByRouteName($name, $minutes);
	}

	public function routeIsTrackable($route)
	{
		return $this->routeRepository->isTrackable($route);
	}

}
