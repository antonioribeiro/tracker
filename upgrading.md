## Laravel Stats Tracker Upgrading Guide

### to 0.5.0

- Download sb-panel v2, if you want to access the new Stats Panel:

```
wget --output-document=/tmp/sba2.zip http://startbootstrap.com/downloads/sb-admin-v2.zip
unzip /tmp/sba2.zip -d public/templates/
```

- Add the following keys to your `app/config/packages/pragmarx/tracker/config.php`:

```
/**
 * Enable the Stats Panel?
 */
'stats_panel_enabled' => false,

/**
 * Stats Panel routes before filter
 *
 * You better drop an 'auth' filter here.
 */
'stats_routes_before_filter' => '',

/**
 * Stats Panel template path
 */
'stats_template_path' => '/templates/sb-admin-v2',

/**
 * Stats Panel base uri.
 *
 * If your site url is http://wwww.mysite.com, then your stats page will be:
 *
 *    http://wwww.mysite.com/stats
 *
 */
'stats_base_uri' => 'stats',

/**
 * Stats Panel layout view
 */
'stats_layout' => 'pragmarx/tracker::layout',

/**
 * Stats Panel controllers namespace
 */
'stats_controllers_namespace' => 'PragmaRX\Tracker\Vendor\Laravel\Controllers',
```

### to 0.4.0

- Add the following keys to your `app/config/packages/pragmarx/tracker/config.php`:

```
'log_geoip' => true,
'log_user_agents' => true,
'log_users' => true,
'log_devices' => true,
'log_referers' => true,
'log_paths' => true,
'log_queries' => true,
'log_routes' => true,
```

- On `tracker_sessions` table, alter columns `device_id` and `agent_id` to be nullable.
- On `tracker_log` table, alter column `path_id` to be nullable.

### to 0.3.2

- Add a is_robot boolean column to: `ALTER TABLE tracker_sessions ADD is_robot BOOL;`
- Add `'do_not_track_robots' => true or false,` to `tracker\config.php`.
- Change `tracker_events_log.class_id` to be a nullable column.
