<?php

namespace PragmaRX\Tracker\Data\Repositories;

use Carbon\Carbon;
use PragmaRX\Support\Config;
use PragmaRX\Support\PhpSession;
use Ramsey\Uuid\Uuid as UUID;

class Session extends Repository
{
    private $config;

    private $session;

    private $sessionInfo;

    protected $relations = ['device', 'user', 'log', 'language', 'agent', 'referer', 'geoIp', 'cookie'];

    public function __construct($model, Config $config, PhpSession $session)
    {
        $this->config = $config;

        $this->session = $session;

        parent::__construct($model);
    }

    public function findByUuid($uuid)
    {
        list($model, $cacheKey) = $this->cache->findCached($uuid, 'uuid', 'PragmaRX\Tracker\Vendor\Laravel\Models\Session');

        if (!$model) {
            $model = $this->newQuery()->where('uuid', $uuid)->with($this->relations)->first();

            $this->cache->cachePut($cacheKey, $model);
        }

        return $model;
    }

    public function getCurrentId($sessionInfo)
    {
        $this->setSessionData($sessionInfo);

        return $this->sessionGetId($sessionInfo);
    }

    public function setSessionData($sessinInfo)
    {
        $this->generateSession($sessinInfo);

        if ($this->sessionIsKnownOrCreateSession()) {
            $this->ensureSessionDataIsComplete();
        }
    }

    private function generateSession($sessionInfo)
    {
        $this->sessionInfo = $sessionInfo;

        if (!$this->sessionIsReliable()) {
            $this->regenerateSystemSession();
        }

        $this->checkSessionUuid();
    }

    private function sessionIsReliable()
    {
        $data = $this->getSessionData();

        if (isset($data['user_id'])) {
            if ($data['user_id'] !== $this->sessionInfo['user_id']) {
                return false;
            }
        }

        if (isset($data['client_ip'])) {
            if ($data['client_ip'] !== $this->sessionInfo['client_ip']) {
                return false;
            }
        }

        if (isset($data['user_agent'])) {
            if ($data['user_agent'] !== $this->sessionInfo['user_agent']) {
                return false;
            }
        }

        return true;
    }

    private function sessionIsKnownOrCreateSession()
    {
        if (!$known = $this->sessionIsKnown()) {
            $this->sessionSetId($this->findOrCreate($this->sessionInfo, ['uuid']));
        } else {
            $session = $this->find($this->getSessionData('id'));

            $session->updated_at = Carbon::now();

            $session->save();

            $this->sessionInfo['id'] = $this->getSessionData('id');
        }

        return $known;
    }

    private function sessionIsKnown()
    {
        if (!$this->session->has($this->getSessionKey())) {
            return false;
        }

        if (!$this->getSessionData('uuid') == $this->getSystemSessionId()) {
            return false;
        }

        if (!$this->findByUuid($this->getSessionData('uuid'))) {
            return false;
        }

        return true;
    }

    private function ensureSessionDataIsComplete()
    {
        $sessionData = $this->getSessionData();

        $wasComplete = true;

        foreach ($this->sessionInfo as $key => $value) {
            if ($key === 'user_agent') {
                continue;
            }
            if ($sessionData[$key] !== $value) {
                if (!isset($model)) {
                    $model = $this->find($this->sessionInfo['id']);
                }

                $model->setAttribute($key, $value);

                $model->save();

                $wasComplete = false;
            }
        }

        if (!$wasComplete) {
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

        if (isset($sessionData['uuid'])) {
            return $sessionData['uuid'];
        }

        return UUID::uuid4()->toString();
    }

    private function regenerateSystemSession($data = null)
    {
        $data = $data ?: $this->getSessionData();

        if (!$data) {
            $this->resetSessionUuid($data);

            $this->sessionIsKnownOrCreateSession();
        }

        return $this->sessionInfo;
    }

    /**
     * @param string $variable
     */
    private function getSessionData($variable = null)
    {
        $data = $this->session->get($this->getSessionKey());

        return $variable
                ? (isset($data[$variable]) ? $data[$variable] : null)
                : $data;
    }

    private function putSessionData($data)
    {
        $this->session->put($this->getSessionKey(), $data);
    }

    private function getSessionKey()
    {
        return $this->config->get('tracker_session_name');
    }

    private function getSessions()
    {
        return $this
                ->newQuery()
                ->with($this->relations)
                ->orderBy('updated_at', 'desc');
    }

    public function all()
    {
        return $this->getSessions()->get();
    }

    public function last($minutes, $returnResults)
    {
        $query = $this
            ->getSessions()
            ->period($minutes);

        if ($returnResults) {
            $cacheKey = 'last-sessions';

            $result = $this->cache->findCachedWithKey($cacheKey);

            if (!$result) {
                $result = $query->get();

                $this->cache->cachePut($cacheKey, $result, 1); // cache only for 1 minute

                return $result;
            }

            return $result;
        }

        return $query;
    }

    public function userDevices($minutes, $user_id, $results)
    {
        if (!$user_id) {
            return [];
        }

        $sessions = $this
            ->getSessions()
            ->period($minutes)
            ->where('user_id', $user_id);

        if ($results) {
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

        foreach ($session->getAttributes() as $name => $value) {
            if (isset($data[$name]) && $name !== 'id' && $name !== 'uuid') {
                $session->{$name} = $data[$name];
            }
        }

        $session->save();

        return $data;
    }

    private function checkIfUserChanged($data, $model)
    {
        if (!is_null($model->user_id) && !is_null($data['user_id']) && $data['user_id'] !== $model->user_id) {
            $newSession = $this->regenerateSystemSession($data);

            $model = $this->findByUuid($newSession['uuid']);
        }

        return $model;
    }

    private function checkSessionUuid()
    {
        if (!isset($this->sessionInfo['uuid']) || !$this->sessionInfo['uuid']) {
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
