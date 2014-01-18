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

class Repository implements RepositoryInterface {

	public function __construct(
									$sessionRepositoryClass,
									$accessRepositoryClass,
									$agentRepositoryClass, 
									$userRepositoryClass,
									MobileDetect $mobileDetect
								)
	{
		$this->sessionRepository = new $sessionRepositoryClass;

		$this->accessRepository = new $accessRepositoryClass;

		$this->agentRepository = new $agentRepositoryClass;

		$this->userRepository = new $userRepositoryClass;

		$this->mobileDetect = $mobileDetect;
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
		return return $this->findOrCreateGeneric($data, 'deviceRepository', array('kind', 'model'));
	}

	private function getCurrentAgent()
	{
		if ( ! isset($_SERVER['HTTP_USER_AGENT']))
		{
			return array(
							'name' => 'not available',
							'platform' => 'not available',
							'platform_version' => 'not available',
							'browser' => 'not available',
							'browser_version' => 'not available',
						);
		}

		$browser = get_browser(null, true);

		$name = $_SERVER['HTTP_USER_AGENT'];

		return array(
						'name' => $name,
						'platform' => $browser['platform'],
						'platform_version' => $browser['platform_version'],
						'browser' => $browser['browser'],
						'browser_version' => $browser['version']
					);
	}

	private function getCurrentDeviceProperties()
	{
		$deviceProperties = $this->mobileDetect->detectDevice();

		return array(
					'kind' => $deviceProperties['kind'],
					'model' => $deviceProperties['model']
				);
	}
}
