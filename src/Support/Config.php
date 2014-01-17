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

namespace PragmaRX\Tracker\Support;

use Illuminate\Config\Repository as IlluminateConfig;

class Config {

    protected $config;

    public function __construct(IlluminateConfig $config, $namespace)
    {
        $this->config = $config;

        $this->namespace = $namespace;
    }

    public function get($key, $default = null)
    {
        return $this->config->get($this->namespace.'::'.$key);
    }

}