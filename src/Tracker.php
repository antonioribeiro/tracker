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

use Illuminate\Http\Request;

use Rhumsaa\Uuid\Uuid as UUID;

class Tracker
{
    private $config;

    private $session;

    public function __construct(
                                    Config $config, 
                                    DataRepositoryManager $dataRepositoryManager, 
                                    Request $request
                                )
    {
        $this->config = $config;

        $this->dataRepositoryManager = $dataRepositoryManager;

        $this->request = $request;
    }

    public function boot()
    {
        if ($this->config->get('enabled'))
        {
            $this->recordAccess();
        }
    }

    public function recordAccess()
    {
        $sessionId = $this->getSessionId();

        if ($this->config->get('log_accesses'))
        {
            $this->access = $this->dataRepositoryManager->createAccess(
                                                                    array(
                                                                            'session_id' => $sessionId,
                                                                            'path_info' => $this->request->path(),
                                                                        )
                                                                );
        }
    }

    public function getSessionId()
    {
        return $this->dataRepositoryManager->getSessionId(
                                                    array(
                                                        'user_id' => $this->getUserId(),
                                                        'device_id' => $this->getDeviceId(),
                                                        'client_ip' => $this->request->getClientIp(),
                                                        'user_agent' => $this->dataRepositoryManager->getCurrentUserAgent(),
                                                        'cookie_id' => $this->getCookieId(),
                                                    ));
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
        return $this
                ->dataRepositoryManager
                ->findOrCreateDevice($this->dataRepositoryManager->getCurrentDeviceProperties());
    }

}