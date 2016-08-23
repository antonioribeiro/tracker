<?php

namespace PragmaRX\Tracker\Data\Repositories;

use Carbon\Carbon;
use PragmaRX\Support\Config;
use Ramsey\Uuid\Uuid as UUID;
use PragmaRX\Support\PhpSession;

class Session extends Repository {

	private $config;

	private $session;

	private $sessionInfo;

	public function __construct($model, Config $config, PhpSession $session)
    {
        $this->config = $config;

	    $this->session = $session;

        parent::__construct($model);
    }

    public function findByUuid($uuid)
    {
        return $this->newQuery()->where('uuid', $uuid)->first();
    }

    public function getCurrentId($sessionInfo)
    {
        $this->setSessionData($sessionInfo);

        return $this->sessionGetId($sessionInfo);
    }

    public function setSessionData($sessinInfo)
    {
        $this->generateSession($sessinInfo);

        if ($this->sessionIsKnownOrCreateSession())
        {
            $this->ensureSessionDataIsComplete();
        }
    }

    private function generateSession($sessionInfo)
    {
        $this->sessionInfo = $sessionInfo;

        if ( ! $this->sessionIsReliable())
        {
            $this->regenerateSystemSession();
        }

	    $this->checkSessionUuid();
    }

    private function sessionIsReliable()
    {
        $data = $this->getSessionData();

        return  isset($data['user_id']) &&
                $data['user_id'] === $this->sessionInfo['user_id'] &&

                isset($data['client_ip']) &&
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
	        $session = $this->find($this->getSessionData('id'));

	        $session->updated_at = Carbon::now();

	        $session->save();

            $this->sessionInfo['id'] = $this->getSessionData('id');
        }

        return $known;
    }

    private function sessionIsKnown()
    {
        return $this->session->has($this->getSessionIdentifier())
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

    private function regenerateSystemSession($data = null)
    {
	    $data = $data ?: $this->getSessionData();

        if ($data)
        {
	        $this->resetSessionUuid($data);

	        $this->sessionIsKnownOrCreateSession();
        }

	    return $this->sessionInfo;
    }

    private function getSessionData($variable = null)
    {
	    $id = $this->getSessionIdentifier();

        $data = $this->session->get($id);

        return $variable ? (isset($data[$variable]) ? $data[$variable] : null) : $data;
    }

    private function putSessionData($data)
    {
        $this->session->put($this->getSessionIdentifier(), $data);
    }

    private function getSessionIdentifier()
    {
        return $this->config->get('tracker_session_name');
    }

    private function getSessions()
    {
        return $this
	            ->newQuery()
	            ->with('user')
		        ->with('device')
		        ->with('agent')
		        ->with('referer')
		        ->with('geoIp')
		        ->with('log')
		        ->with('cookie')
	            ->orderBy('updated_at', 'desc');
    }

    public function all()
    {
        return $this->getSessions()->get();
    }

    public function last($minutes, $results)
    {
	    $query = $this
		            ->getSessions()
		            ->period($minutes);

	    if ($results)
	    {
		    return $query->get();
	    }

	    return $query;
    }

    public function userDevices($minutes, $user_id, $results) {
        if ( ! $user_id)
        {
            return [];
        }

        $sessions = $this
            ->getSessions()
            ->period($minutes)
            ->where('user_id', $user_id);

        if ($results)
        {
            $sessions = $sessions->get()->pluck('device')->unique();
        }

        return $sessions;
    }

    public function users($minutes, $results)
    {
         return $this->getModel()->users($minutes, $results);
    }

	public function getCurrent()
	{
		return $this->getModel();
	}

	public function updateSessionData($data)
	{
		$session = $this->checkIfUserChanged($data, $this->find($this->getSessionData('id')));

		foreach($session->getAttributes() as $name => $value)
		{
			if (isset($data[$name]) && $name !== 'id' && $name !== 'uuid')
			{
				$session->{$name} = $data[$name];
			}
		}

		$session->save();

		return $data;
	}

	private function checkIfUserChanged($data, $model)
	{
		if ( ! is_null($model->user_id) && ! is_null($data['user_id']) && $data['user_id'] !== $model->user_id)
		{
			$newSession = $this->regenerateSystemSession($data);

			$model = $this->findByUuid($newSession['uuid']);
		}

		return $model;
	}

	private function checkSessionUuid()
	{
		if ( ! isset($this->sessionInfo['uuid']) || ! $this->sessionInfo['uuid'])
		{
			$this->sessionInfo['uuid'] = $this->getSystemSessionId();
		}
	}

	private function resetSessionUuid($data = null)
	{
		$this->sessionInfo['uuid'] = null;

		$data = $data ?: $this->sessionInfo;

		unset($data['uuid']);

		$this->putSessionData($data);

		$this->checkSessionUuid();

		return $data;
	}

}
