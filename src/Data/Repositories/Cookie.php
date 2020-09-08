<?php

namespace PragmaRX\Tracker\Data\Repositories;

use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use PragmaRX\Support\Config;
use PragmaRX\Tracker\Vendor\Laravel\Models\Session;
use Ramsey\Uuid\Uuid as UUID;

class Cookie extends Repository
{
    private $config;

    private $request;

    private $cookieJar;

    public function __construct($model, Config $config, Request $request, CookieJar $cookieJar)
    {
        $this->config = $config;

        $this->request = $request;

        $this->cookieJar = $cookieJar;

        parent::__construct($model);
    }

    public function getId()
    {
        if (!$this->config->get('store_cookie_tracker')) {
            return;
        }

        if (!$cookie = $this->request->cookie($this->config->get('tracker_cookie_name'))) {
            $cookie = UUID::uuid4()->toString();
            $this->cookieJar->queue($this->config->get('tracker_cookie_name'), $cookie, 2628000);
        }

        return $this->findOrCreate(['uuid' => $cookie]);
    }
}
