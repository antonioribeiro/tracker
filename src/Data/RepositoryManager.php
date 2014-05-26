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
 * @version    1.0.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data;

use PragmaRX\Tracker\Support\MobileDetect;
use PragmaRX\Tracker\Support\UserAgentParser;
use PragmaRX\Tracker\Support\Config;

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

use PragmaRX\Tracker\Services\Authentication;

use Illuminate\Session\Store as IlluminateSession;

use Rhumsaa\Uuid\Uuid as UUID;

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

	public function __construct(
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
                                    MobileDetect $mobileDetect,
                                    UserAgentParser $userAgentParser,
                                    Authentication $authentication,
                                    IlluminateSession $session,
                                    Config $config
                                )
    {
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

        $this->authentication = $authentication;

        $this->mobileDetect = $mobileDetect;

        $this->userAgentParser = $userAgentParser;

        $this->session = $session;

        $this->config = $config;
    }

    public function createLog($data)
    {
        return $this->logRepository->create($data);
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

        $properties['agent_id'] = $this->getAgentId();

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
			array('domain' => $domain),
			array('domain')
		);
	}

	public function getRefererId($referer)
	{
		if ($referer)
		{
			$url = parse_url($referer);

			$parts = explode(".", $url['host']);

			$domain_id = $this->getDomainId($parts[count($parts)-2] . "." . $parts[count($parts)-1]);

			return $this->refererRepository->findOrCreate(
				array(
					'referer' => $referer,
					'host' => $url['host'],
					'domain_id' => $domain_id,
				),
				array('referer')
			);
		}
	}

	public function getRoutePathId($route, $request)
	{
		$route_id = $this->getRouteId(
			$route->currentRouteName(),
			$route->currentRouteAction()
		);

		$created = false;

		$route_path_id = $this->getRoutePath(
			$route_id,
			$request->path(),
			$created
		);

		if ($created)
		{
			foreach($route->current()->parameters() as $parameter => $value)
			{
				$this->createRoutePathParameter($route_path_id, $parameter, $value);
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

	private function getRoutePath($route_id, $path, $created)
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

}
