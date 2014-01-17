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
									$userRepositoryClass
								)
	{
		$this->sessionRepository = new $sessionRepositoryClass;
		$this->accessRepository = new $accessRepositoryClass;
		$this->agentRepository = new $agentRepositoryClass;
		$this->userRepository = new $userRepositoryClass;
	}

	public function createAccess($data)
	{
		return $this->accessRepository->create(
												array(
														'session_id' => $data['session_id'],
													)
											);
	}

	public function findOrCreateSession($data)
	{
		if (! $model = $this->sessionRepository->where('session_uuid', $data['session_uuid'])->first())
		{
			$model = $this->sessionRepository->create($data);
		}

		return $model;
	}
}
