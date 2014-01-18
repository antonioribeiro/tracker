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

use PragmaRX\Tracker\Support\Config as Config;
use PragmaRX\Tracker\Data\Repository as DataRepository;

use Illuminate\Session\Store as Session;
use Illuminate\Http\Request;

use Rhumsaa\Uuid\Uuid as UUID;

class Tracker
{
    private $config;

    private $session;

    private $agentUUID;

    private $sessionUUID;

    private $userUUID;

    private $deviceUUID;

    public function __construct(Config $config, Session $session, DataRepository $dataRepository, Request $request)
    {
        $this->config = $config;

        $this->session = $session;

        $this->dataRepository = $dataRepository;

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
        $this->access = $this->dataRepository->createAccess(
                                                                array(
                                                                        'session_id' => $this->getSessionId(),
                                                                        'path_info' => $this->request->path(),
                                                                    )
                                                            );
    }

    public function getSessionId()
    {
        return $this->dataRepository->findOrCreateSession(
                                                            array(
                                                                    'session_uuid' => $this->session->getId(),
                                                                    'user_id' => $this->getUserId(),
                                                                    'device_id' => $this->getDeviceId(),
                                                                    'client_ip' => $this->request->getClientIp(),
                                                                )
                                                        );
    }

    public function getUserId()
    {
        return 1; //(string) UUID::uuid4();
    }

    public function getDeviceId()
    {
        return $this->dataRepository->findOrCreateDevice($this->dataRepository->getCurrentDeviceProperties());
    }

        // /**
        //  * Get or set and get a Session id that will change at every Session
        //  * 
        //  * @param type $sessionUUID 
        //  * @return string
        //  */
        // public function getSessionUUID($sessionUUID = null)
        // {
        //         if (!empty($sessionUUID)) return $sessionUUID;

        //         return $this->makeSessionUUID();

        // }

        // public function makeSessionUUID()
        // {
        //         if (!Session::has('sessionUUID'))
        //         {
        //                 $sessionUUID = UUID::v4();
        //                 Session::put('sessionUUID', $sessionUUID);
        //         }
        //         else
        //         {
        //                 $sessionUUID = Session::get('sessionUUID');
        //         }

        //         return $sessionUUID;
        // }

        // /**
        //  * Get or set and get a user uuid that will change at every Session
        //  * 
        //  * This value may be changed by LogonController if a user logs in
        //  * 
        //  * @param type $sessionUUID 
        //  * @return string
        //  */
        // public function getUserUUID($userUUID = null)
        // {
        //         if (!empty($userUUID)) return $userUUID;

        //         return $this->makeUserUUID();
        // }

        // public function makeUserUUID()
        // {
        //         if (!Session::has('userUUID'))
        //         {
        //                 $userUUID = UUID::v4();
        //                 Session::put('userUUID', $userUUID);
        //         }
        //         else
        //         {
        //                 $userUUID = Session::get('userUUID');
        //         }

        //         return $userUUID;
        // }

        // /**
        //  * Get or set and get a device id
        //  * 
        //  * This will be stored in a cookie and, if possible, never change
        //  * 
        //  * @param type $sessionUUID 
        //  * @return string
        //  */
        // public function getDeviceUUID($deviceUUID = null)
        // {
        //         if (!empty($deviceUUID)) return $deviceUUID;

        //         return $this->makeDeviceUUID();

        // }

        // public function makeDeviceUUID()
        // {
        //         $cookie = $this->getDeviceUUIDCookie();

        //         if (!Session::has('deviceUUID'))
        //         {
        //                 if (empty($cookie))
        //                 {
        //                         $deviceUUID = UUID::v4();
        //                 }
        //                 else
        //                 {
        //                         $deviceUUID = $cookie->value;
        //                 }

        //                 Session::put('deviceUUID', $deviceUUID);
        //         }                
        //         else
        //         {
        //                 $deviceUUID = Session::get('deviceUUID');
        //         }
        
        //         if (empty($cookie))
        //         {
        //                 $cookie = $this->makeDeviceUUIDCookie($deviceUUID);
        //         }

        //         return $deviceUUID;
        // }

        // public function checkDeviceUUIDCookie()
        // {
        //         $cookie = Cookie::get('vevey-duuid');

        //         if ( empty($cookie) )
        //         {
        //                 $this->makedeviceUUID(); /// will also make the cookie
        //                 return false;
        //         }

        //         return true;
        // }

        // public function getDeviceUUIDCookie()
        // {
        //         $cookie = Cookie::get('vevey-duuid');

        //         return !empty($cookie) ? $cookie : Session::get('vevey-cookie');
        // }

        // public function makeDeviceUUIDCookie($deviceUUID)
        // {
        //         return Cookie::forever('vevey-duuid', $deviceUUID);
        // }
}