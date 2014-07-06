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
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Tracker\Support\Config;

use Illuminate\Http\Request;

use Illuminate\Cookie\CookieJar;

use Rhumsaa\Uuid\Uuid as UUID;

class Cookie extends Repository {

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
        if ( ! $this->config->get('store_cookie_tracker'))
        {
            return;
        }

        if ( ! $cookie = $this->request->cookie($this->config->get('tracker_cookie_name')))
        {
            $cookie = (string) UUID::uuid4();

            $this->cookieJar->queue($this->config->get('tracker_cookie_name'), $cookie, 0);
        }

        return $this->findOrCreate(array('uuid' => $cookie));
    }

}
