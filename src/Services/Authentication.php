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

namespace PragmaRX\Tracker\Services;

use PragmaRX\Tracker\Support\Config as Config;

use Illuminate\Foundation\Application;

class Authentication {

    private $config;

    private $authentication;

    public function __construct(Config $config, Application $app)
    {
        $this->config = $config;

        $class = $this->config->get('authentication_facade');

        $this->authentication = $app->make($this->config->get('authentication_ioc_binding'));
    }

    public function check()
    {
        $method = $this->config->get('authenticated_check_method');

        return $this->authentication->{$method}();
    }

    public function user()
    {
        $method = $this->config->get('authenticated_user_method');

        return $this->authentication->{$method}();
    }

    public function getCurrentUserId()
    {
        if ($this->check())
        {
            return $this->user()->{$this->config->get('authenticated_user_id_column')};
        }
    }
}
