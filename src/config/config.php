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

return array(

    'create_tracker_alias' => true,

    'tracker_alias' => 'Tracker',

	'enabled' => false,

	'log_enabled' => false,

    'store_cookie_tracker' => true,

    'connection' => null,

    'tracker_session_name' => 'tracker_session',

    'tracker_cookie_name' => 'please_change_this_cookie_name',

    'user_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\User',

    'session_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Session',

    'log_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Log',

	'path_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Path',

	'query_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Query',

	'query_argument_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\QueryArgument',

	'agent_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Agent',

    'device_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Device',

    'cookie_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Cookie',

    'authentication_ioc_binding' => 'auth',

    'authenticated_check_method' => 'check',

    'authenticated_user_method' => 'user',

    'authenticated_user_id_column' => 'id',

);
