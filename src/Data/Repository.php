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

use Illuminate\Session\Store as Session;

class Repository implements RepositoryInterface {

	public function __construct(
									$sessionRepositoryClass,
									$accessRepositoryClass,
									$agentRepositoryClass, 
									$deviceRepositoryClass,
									$authentication,
									MobileDetect $mobileDetect,
                                    UserAgentParser $userAgentParser,
                                    Session $session,
									Config $config
								)
	{
		$this->sessionRepository = new $sessionRepositoryClass;

		$this->accessRepository = new $accessRepositoryClass;

		$this->agentRepository = new $agentRepositoryClass;

		$this->deviceRepository = new $deviceRepositoryClass;

		$this->authentication = $authentication;

		$this->mobileDetect = $mobileDetect;

		$this->userAgentParser = $userAgentParser;

		$this->session = $session;

		$this->config = $config;
	}

	public function createAccess($data)
	{
		return $this->accessRepository->create($data);
	}

	public function findOrCreateGeneric($data, $repository, array $keys)
	{
		$model = $this->$repository->newQuery();

		foreach($keys as $key)
		{
			$model = $model->where($key, $data[$key]);
		}

		if (! $model = $model->first())
		{
			$model = $this->$repository->create($data);
		}

		return $model->id;
	}

	public function findOrCreateSession($data)
	{
		return $this->findOrCreateGeneric($data, 'sessionRepository', array('session_uuid'));
	}

	public function findOrCreateAgent($data)
	{
		return $this->findOrCreateGeneric($data, 'agentRepository', array('name'));
	}

	public function findOrCreateDevice($data)
	{
		return $this->findOrCreateGeneric($data, 'deviceRepository', array('kind', 'model', 'platform', 'platform_version'));
	}

    public function getAgentId()
    {
        return $this->findOrCreateAgent($this->getCurrentAgent());
    }

	public function getCurrentAgent()
	{
		return array(
						'name' => $this->userAgentParser->originalUserAgent ?: 'Other',

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

    public function getSessionId($sessionInfo)
    {
        // $this->session->forget($this->config->get('tracker_session_name'));
        // dd('forgot');

    	$sessionInfo['session_uuid'] = $this->session->getId();

        $this->sessionInfo = $sessionInfo;
        
        if ($this->sessionIsKnownOrCreateSession())
        {
            $this->ensureSessionDataIsComplete();
        }

        return $this->sessionGetId();
    }

    private function sessionIsKnownOrCreateSession()
    {
        $known = $this->session->has($this->config->get('tracker_session_name')) &&
                    $this->session->get($this->config->get('tracker_session_name'))['session_uuid'] == $this->session->getId();

        if (! $known)
        {
            $this->sessionSetId($this->findOrCreateSession($this->sessionInfo));
        }
        else
        {
        	$this->sessionInfo['id'] = $this->session->get($this->config->get('tracker_session_name'))['id'];
        }

        return $known;
    }

    private function ensureSessionDataIsComplete()
    {
    	$sessionData = $this->session->get($this->config->get('tracker_session_name'));

    	foreach ($this->sessionInfo as $key => $value) {
    		if ($sessionData[$key] !== $value)
    		{
    			if ( ! isset($model))
    			{
		    		$model = $this->sessionRepository->find($this->sessionInfo['id']);
    			}

    			$model->{$key} = $value;
    			$model->save();
    		}
    	}
    }

    private function sessionGetId()
    {
        return $this->sessionInfo['id'];
    }

    private function sessionSetId($id)
    {
        $this->sessionInfo['id'] = $id;

        $this->storeSession();
    }

    private function storeSession()
    {
        $this->session->put($this->config->get('tracker_session_name'), $this->sessionInfo);
    }

}
