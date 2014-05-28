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

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Tracker\Support\Config;

use Illuminate\Session\Store as IlluminateSession;

use Rhumsaa\Uuid\Uuid as UUID;

class Session extends Repository {

    public function __construct($model, Config $config, IlluminateSession $session)
    {
        $this->config = $config;

        $this->session = $session;

        parent::__construct($model);
    }

    public function getCurrentId($sessionInfo)
    {
        $this->setSessionData($sessionInfo);

        return $this->sessionGetId($sessionInfo);
    }

    private function setSessionData($sessinInfo)
    {
        $this->generateSession($sessinInfo);

        if ($this->sessionIsKnownOrCreateSession())
        {
            $this->ensureSessionDataIsComplete();
        }
    }

    public function generateSession($sessionInfo)
    {
        $this->sessionInfo = $sessionInfo;

        if ( ! $this->sessionIsReliable())
        {
            $this->regenerateSystemSession();
        }

        $this->sessionInfo['uuid'] = $this->getSystemSessionId();

        $this->sessionInfo['last_activity'] = \Carbon\Carbon::now();
    }

    private function sessionIsReliable()
    {
        $data = $this->getSessionData();

        return  isset($data['client_ip']) &&
                $data['client_ip'] === $this->sessionInfo['client_ip'] &&
                isset($data['user_agent']) &&
                $data['user_agent'] === $this->sessionInfo['user_agent'];
    }

    private function sessionIsKnownOrCreateSession()
    {
        if ( ! $known = $this->sessionIsKnown())
        {
            $this->sessionSetId($this->findOrCreate($this->sessionInfo, array('uuid')));
        }
        else
        {
            $this->sessionInfo['id'] = $this->getSessionData('id');
        }

        return $known;
    }

    private function sessionIsKnown()
    {
        return $this->session->has($this->getSessioIdentifier()) 
                && $this->getSessionData('uuid') == $this->getSystemSessionId()
                && $this->where('uuid', $this->getSessionData('uuid'))->first();
    }

    private function ensureSessionDataIsComplete()
    {
        $sessionData = $this->getSessionData();

        $wasComplete = true;

        foreach ($this->sessionInfo as $key => $value)
        {
            if ($sessionData[$key] !== $value)
            {
                if ( ! isset($model))
                {
                    $model = $this->find($this->sessionInfo['id']);
                }

                $model->setAttribute($key, $value);

                $model->save();

                $wasComplete = false;
            }
        }

        if (! $wasComplete)
        {
            $this->storeSession();
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
        $this->putSessionData($this->sessionInfo);
    }

    private function getSystemSessionId()
    {
        $sessionData = $this->getSessionData();

        return isset($sessionData['uuid']) 
                ? $sessionData['uuid'] 
                : (string) UUID::uuid4();
    }

    public function regenerateSystemSession()
    {
        if ($data = $this->getSessionData())
        {
            unset($data['uuid']);

            $this->sessionInfo['uuid'] = null;

            $this->putSessionData($data);
        }
    }

    private function getSessionData($variable = null)
    {
        $data = $this->session->get($this->getSessioIdentifier());

        return $variable ? (isset($data[$variable]) ? $data[$variable] : null) : $data;
    }

    private function putSessionData($data)
    {
        $this->session->put($this->getSessioIdentifier(), $data);
    }

    private function getSessioIdentifier()
    {
        return $this->config->get('tracker_session_name');
    }

    public function getOpenSessions()
    {
        return $this->model->orderBy('updated_at', 'desc')->get();
    }
}
