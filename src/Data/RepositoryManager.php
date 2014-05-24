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
use PragmaRX\Tracker\Data\Repositories\Agent;
use PragmaRX\Tracker\Data\Repositories\Device;
use PragmaRX\Tracker\Data\Repositories\Cookie;

use PragmaRX\Tracker\Services\Authentication;

use Illuminate\Session\Store as IlluminateSession;

use Rhumsaa\Uuid\Uuid as UUID;

class RepositoryManager implements RepositoryManagerInterface {

    public function __construct(
                                    Session $sessionRepository,
                                    Log $logRepository,
                                    Agent $agentRepository,
                                    Device $deviceRepository,
                                    Cookie $cookieRepository,
                                    MobileDetect $mobileDetect,
                                    UserAgentParser $userAgentParser,
                                    Authentication $authentication,
                                    IlluminateSession $session,
                                    Config $config
                                )
    {
        $this->sessionRepository = $sessionRepository;

        $this->logRepository = $logRepository;

        $this->agentRepository = $agentRepository;

        $this->deviceRepository = $deviceRepository;

        $this->cookieRepository = $cookieRepository;

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

}
