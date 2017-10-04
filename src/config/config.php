<?php

return [

    /*
     * Enable it?
     */
    'enabled' => false,

    /*
     * Enable cache?
     */
    'cache_enabled' => true,

    /*
     * Deffer booting for middleware use
     */
    'use_middleware' => false,

    /*
     * Robots should be tracked?
     */
    'do_not_track_robots' => false,

    /*
     * Which environments are not trackable?
     */
    'do_not_track_environments' => [
        // defaults to none
    ],

    /*
     * Which routes names are not trackable?
     */
    'do_not_track_routes' => [
        'tracker.stats.*',
    ],

    /*
     * Which route paths are not trackable?
     */
    'do_not_track_paths' => [
        'api/*',
    ],

    /*
     * The Do Not Track Ips is used to disable Tracker for some IP addresses:
     *
     *     '127.0.0.1', '192.168.1.1'
     *
     * You can set ranges of IPs
     *     '192.168.0.1-192.168.0.100'
     *
     * And use net masks
     *     '10.0.0.0/32'
     *     '172.17.0.0/255.255.0.0'
     */
    'do_not_track_ips' => [
        '127.0.0.0/24', /// range 127.0.0.1 - 127.0.0.255
    ],

    /*
     * When an IP is not trackable, show a message in log.
     */
    'log_untrackable_sessions' => true,

    /*
     * Log every single access?
     *
     * The log table can become huge if your site is popular, but...
     *
     * Log table is also responsible for storing information on:
     *
     *    - Routes and controller actions accessed
     *    - HTTP method used (GET, POST...)
     *    - Error log
     *    - URL queries (including values)
     */
    'log_enabled' => false,

    /*
     * Log artisan commands?
     */
    'console_log_enabled' => false,

    /*
     * Log SQL queries?
     *
     * Log must be enabled for this option to work.
     */
    'log_sql_queries' => false,

    /*
     * If you prefer to store Tracker data on a different database or connection,
     * you can set it here.
     *
     * To avoid SQL queries log recursion, create a different connection for Tracker,
     * point it to the same database (or not) and forbid logging of this connection in
     * do_not_log_sql_queries_connections.
     */
    'connection' => 'tracker',

    /*
     * Forbid logging of SQL queries for some connections.
     *
     * To avoid recursion, you better ignore Tracker connection here.
     *
     * Please create a separate database connection for Tracker. It can hit
     * the same database of your application, but the connection itself
     * has to have a different name, so the package can ignore its own queries
     * and avoid recursion.
     *
     */
    'do_not_log_sql_queries_connections' => [
        'tracker',
    ],

    /*
     * GeoIp2 database path.
     *
     * To get a fresh version of this file, use the command
     *
     *      php artisan tracker:updategeoip
     *
     */

    'geoip_database_path' => __DIR__.'/geoip', //storage_path('geoip'),

    /*
     * Also log SQL query bindings?
     *
     * Log must be enabled for this option to work.
     */
    'log_sql_queries_bindings' => false,

    /*
     * Log events?
     */
    'log_events' => false,

    /*
     * Which events do you want to log exactly?
     */
    'log_only_events' => [
        // defaults to logging all events
    ],

    /*
     * What are the names of the id columns on your system?
     *
     * 'id' is the most common, but if you have one or more different,
     * please add them here in your preference order.
     */
    'id_columns_names' => [
        'id',
    ],
    /*
     * Do not log events for the following patterns.
     * Strings accepts wildcards:
     *
     *    eloquent.*
     *
     */
    'do_not_log_events' => [
        'illuminate.log',
        'eloquent.*',
        'router.*',
        'composing: *',
        'creating: *',
    ],

    /*
     * Do you wish to log Geo IP data?
     *
     * You will need to install the geoip package
     *
     *     composer require "geoip/geoip":"~1.14"
     *
     * And remove the PHP module
     *
     *     sudo apt-get purge php5-geoip
     *
     */
    'log_geoip' => false,

    /*
     * Do you wish to log the user agent?
     */
    'log_user_agents' => false,

    /*
     * Do you wish to log your users?
     */
    'log_users' => false,

    /*
     * Do you wish to log devices?
     */
    'log_devices' => false,

    /*
     * Do you wish to log languages?
     */
    'log_languages' => false,

    /*
     * Do you wish to log HTTP referers?
     */
    'log_referers' => false,

    /*
     * Do you wish to log url paths?
     */
    'log_paths' => false,

    /*
     * Do you wish to log url queries and query arguments?
     */
    'log_queries' => false,

    /*
     * Do you wish to log routes and route parameters?
     */
    'log_routes' => false,

    /*
     * Log errors and exceptions?
     */
    'log_exceptions' => false,

    /*
     * A cookie may be created on your visitor device, so you can have information
     * on everything made using that device on your site.	 *
     */
    'store_cookie_tracker' => false,

    /*
     * If you are storing cookies, you better change it to a name you of your own.
     */
    'tracker_cookie_name' => 'please_change_this_cookie_name',

    /*
     * Internal tracker session name.
     */
    'tracker_session_name' => 'tracker_session',

    /*
     * ** IMPORTANT **
     *   Change the user model to your own.
     */
    'user_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\User',

    /*
     * You can use your own model for every single table Tracker has.
     */

    'session_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Session',

    'log_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Log',

    'path_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Path',

    'query_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Query',

    'query_argument_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\QueryArgument',

    'agent_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Agent',

    'device_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Device',

    'cookie_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Cookie',

    'domain_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Domain',

    'referer_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Referer',

    'referer_search_term_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\RefererSearchTerm',

    'route_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Route',

    'route_path_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\RoutePath',

    'route_path_parameter_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\RoutePathParameter',

    'error_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Error',

    'geoip_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\GeoIp',

    'sql_query_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\SqlQuery',

    'sql_query_binding_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\SqlQueryBinding',

    'sql_query_binding_parameter_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\SqlQueryBindingParameter',

    'sql_query_log_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\SqlQueryLog',

    'connection_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Connection',

    'event_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Event',

    'event_log_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\EventLog',

    'system_class_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\SystemClass',

    'language_model' => 'PragmaRX\Tracker\Vendor\Laravel\Models\Language',

    /*
     * Laravel internal variables on user authentication and login.
     */
    'authentication_ioc_binding' => ['auth'], // defaults to 'auth' in Illuminate\Support\Facades\Auth

    'authenticated_check_method' => 'check', // to Auth::check()

    'authenticated_user_method' => 'user', // to Auth::user()

    'authenticated_user_id_column' => 'id', // to Auth::user()->id

    'authenticated_user_username_column' => 'email', // to Auth::user()->email

    /*
     * Enable the Stats Panel?
     */
    'stats_panel_enabled' => false,

    /*
     * Stats Panel routes before filter
     *
     */
    'stats_routes_before_filter' => '',

    /*
     * Stats Panel routes after filter
     *
     */
    'stats_routes_after_filter' => '',

    /*
     * Stats Panel routes middleware
     *
     */
    'stats_routes_middleware' => 'web',

    /*
     * Stats Panel template path
     */
    'stats_template_path' => '/templates/sb-admin-2',

    /*
     * Stats Panel base uri.
     *
     * If your site url is http://wwww.mysite.com, then your stats page will be:
     *
     *    http://wwww.mysite.com/stats
     *
     */
    'stats_base_uri' => 'stats',

    /*
     * Stats Panel layout view
     */
    'stats_layout' => 'pragmarx/tracker::layout',

    /*
     * Stats Panel controllers namespace
     */
    'stats_controllers_namespace' => 'PragmaRX\Tracker\Vendor\Laravel\Controllers',
];
