# Laravel Stats Tracker Upgrading Guide

## from 2.0.x to 3.0.0 

Add cache_enabled key to your config\tracker.php:

    /*
     * Enable cache?
     */
    'cache_enabled' => true,

## from 2.0.x to 2.0.4 or 2.0.9 

#### Run Artisan tracker:tables command

    php artisan tracker:tables

#### Migrate

    php artisan migrate

#### If you already have executed and get the error "1215 Cannot add foreign key constraint"

You'll have to upgrade your migrations:

1 - Rollback the last one: `php artisan tracker:tables`

2 - Delete the following files from your database\migrations:

    2015_11_23_311097_create_tracker_languages_table.php    
    2015_11_23_311098_add_language_id_column_to_sessions.php    
    2015_11_23_311099_add_tracker_language_foreign_key_to_sessions.php  
    2015_11_23_311100_add_nullable_to_tracker_error.php

3 - Run `php artisan tracker:tables` to upgrade them

4 - Migrate: `php artisan migrate`

## to 0.6.0

A [massive update happened at StartBootstrap](https://github.com/IronSummitMedia/startbootstrap/commit/66716399cf8eb5109498d41a2dad95a093c18f2b), you need to download and unzip the admin frontend again:
 
```
    rm -rf public/templates/sb-admin-v2
    wget --output-document=/tmp/sba2.zip http://startbootstrap.com/downloads/sb-admin-2.zip
    unzip /tmp/sba2.zip -d public/templates/
```

## to 0.5.1

#### As `tracker_route_paths.route_id` column was wrongly set to string, you need to change it to int8 or bigint. This is how you do this
 
##### In PostgreSQL 
```
ALTER TABLE "tracker_route_paths" ALTER COLUMN route_id TYPE BIGINT 
    USING CAST(CASE route_id WHEN '' THEN NULL ELSE route_id END AS BIGINT)
```

##### In MySQL
```
ALTER TABLE tracker_route_paths CHANGE route_id route_id bigint unsigned NULL;
```

#### Add the following keys to your `app/config/packages/pragmarx/tracker/config.php`:

```
'log_exceptions' => true,

'authenticated_user_username_column' => 'email',

'do_not_track_routes' => array(
    'tracker.stats.*',
),
```
   
## to 0.5.0

#### Download sb-panel v2, if you want to access the new Stats Panel:

```
wget --output-document=/tmp/sba2.zip http://startbootstrap.com/downloads/sb-admin-v2.zip
unzip /tmp/sba2.zip -d public/templates/
```

#### Add the following keys to your `app/config/packages/pragmarx/tracker/config.php`:

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

#### The Stats Panel must be enabled in your config file

```
'stats_panel_enabled' => true,
```

## to 0.4.0

#### Add the following keys to your `app/config/packages/pragmarx/tracker/config.php`:

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

#### On `tracker_sessions` table, alter columns `device_id` and `agent_id` to be nullable.
#### On `tracker_log` table, alter column `path_id` to be nullable.

## to 0.3.2

- Add a is_robot boolean column to: `ALTER TABLE tracker_sessions ADD is_robot BOOL;`
- Add `'do_not_track_robots' => true or false,` to `tracker\config.php`.
- Change `tracker_events_log.class_id` to be a nullable column.
