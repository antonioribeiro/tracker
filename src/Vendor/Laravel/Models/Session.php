<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Session extends Base
{
    protected $table = 'tracker_sessions';

    protected $fillable = [
        'uuid',
        'user_id',
        'device_id',
        'language_id',
        'agent_id',
        'client_ip',
        'cookie_id',
        'referer_id',
        'geoip_id',
        'is_robot',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo($this->getConfig()->get('user_model'));
    }

    public function device()
    {
        return $this->belongsTo($this->getConfig()->get('device_model'));
    }

    public function language()
    {
        return $this->belongsTo($this->getConfig()->get('language_model'));
    }

    public function agent()
    {
        return $this->belongsTo($this->getConfig()->get('agent_model'));
    }

    public function referer()
    {
        return $this->belongsTo($this->getConfig()->get('referer_model'));
    }

    public function geoIp()
    {
        return $this->belongsTo($this->getConfig()->get('geoip_model'), 'geoip_id');
    }

    public function cookie()
    {
        return $this->belongsTo($this->getConfig()->get('cookie_model'), 'cookie_id');
    }

    public function log()
    {
        return $this->hasMany($this->getConfig()->get('log_model'));
    }

    public function getPageViewsAttribute()
    {
        return $this->log()->count();
    }

    public function users($minutes, $result)
    {
        $query = $this
            ->select(
                'user_id',
                $this->getConnection()->raw('max(updated_at) as updated_at')
            )
            ->groupBy('user_id')
            ->from('tracker_sessions')
            ->period($minutes)
            ->whereNotNull('user_id')
            ->orderBy($this->getConnection()->raw('max(updated_at)'), 'desc');

        if ($result) {
            return $query->get();
        }

        return $query;
    }

    public function userDevices($minutes, $result, $user_id)
    {
        $query = $this
            ->select(
                'user_id',
                $this->getConnection()->raw('max(updated_at) as updated_at')
            )
            ->groupBy('user_id')
            ->from('tracker_sessions')
            ->period($minutes)
            ->whereNotNull('user_id')
            ->orderBy($this->getConnection()->raw('max(updated_at)'), 'desc');

        if ($result) {
            return $query->get();
        }

        return $query;
    }
}
