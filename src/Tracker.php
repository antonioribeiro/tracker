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

namespace PragmaRX\Tracker;

use PragmaRX\Tracker\Support\Config;
use PragmaRX\Tracker\Data\RepositoryManager as DataRepositoryManager;
use PragmaRX\Tracker\Support\Database\Migrator as Migrator;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Tracker
{
    private $config;

    private $session;
	/**
	 * @var \Illuminate\Routing\Router
	 */
	private $route;

	public function __construct(
                                    Config $config,
                                    DataRepositoryManager $dataRepositoryManager,
                                    Request $request,
                                    Router $route,
                                    Migrator $migrator
                                )
    {
        $this->config = $config;

        $this->dataRepositoryManager = $dataRepositoryManager;

        $this->request = $request;

        $this->migrator = $migrator;

	    $this->route = $route;
    }

    public function boot()
    {
        if ($this->config->get('enabled'))
        {
            $this->log();
        }
    }

    public function log()
    {
        if ($this->config->get('log_enabled'))
        {
            $this->dataRepositoryManager->createLog(
                array(
                    'session_id' => $this->getSessionId(true),
                    'method' => $this->request->method(),
                    'path_id' => $this->getPathId(),
                    'query_id' => $this->getQueryId(),
                    'is_ajax' => $this->request->ajax(),
                    'is_secure' => $this->request->isSecure(),
                    'is_json' => $this->request->isJson(),
                    'wants_json' => $this->request->wantsJson(),
                )
            );
        }
    }

    public function getSessionId($updateLastActivity = false)
    {
        return $this->dataRepositoryManager->getSessionId(
            array(
                'user_id' => $this->getUserId(),
                'device_id' => $this->getDeviceId(),
                'client_ip' => $this->request->getClientIp(),
                'agent_id' => $this->dataRepositoryManager->getAgentId(),
                'user_agent' => $this->dataRepositoryManager->getCurrentUserAgent(),
	            'referer_id' => $this->getRefererId(),
                'cookie_id' => $this->getCookieId(),
            ),
            $updateLastActivity
        );
    }

    public function getUserId()
    {
        return $this->dataRepositoryManager->getCurrentUserId();
    }

    public function getCookieId()
    {
        return $this->dataRepositoryManager->getCookieId();
    }

    public function getDeviceId()
    {
        return $this->dataRepositoryManager->findOrCreateDevice(
	        $this->dataRepositoryManager->getCurrentDeviceProperties()
        );
    }

	public function getPathId()
	{
		return $this->dataRepositoryManager->findOrCreatePath(
			array(
				'path' => $this->request->path()
			)
		);
	}

	public function getQueryId()
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

    public function getMigrator()
    {
        return $this->migrator;
    }

	public function routerMatched()
	{
		if ($this->config->get('enabled'))
		{
			return $this->dataRepositoryManager->updateRoute(
				$this->getRoutePathId($this->route->current())
			);
		}
	}

	private function getRefererId()
	{
		return $this->dataRepositoryManager->getRefererId(
			$this->request->headers->get('referer')
		);
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

    public function lastSessions($minutes = 1440)
    {
        return $this->dataRepositoryManager->getLastSessions($minutes);
    }

    public function allSessions()
    {
        return $this->dataRepositoryManager->getAllSessions();
    }

}