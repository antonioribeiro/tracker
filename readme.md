# Laravel Stats Tracker

[![Latest Stable Version](https://poser.pugx.org/pragmarx/tracker/v/stable.png)](https://packagist.org/packages/pragmarx/tracker) [![License](https://poser.pugx.org/pragmarx/tracker/license.png)](https://packagist.org/packages/pragmarx/tracker)

###Tracker gathers a lot of information from your requests to identify and store:

- **Sessions**
- **Page Views (hits on routes)**
- **Users (logged users)**
- **Devices** (computer, smartphone, tablet...)
- **User Devices** (by, yeah, storing a cookie on each device)
- **Browsers** (Chrome, Mozilla Firefox, Safari, Internet Explorer...)
- **Operating Systems** (iOS, Mac OS, Linux, Windows...)
- **Geo Location Data** (Latitute, Longitude, Country and City)
- **Routes and all its parameters**
- **Events**
- **Referers**
- **Exceptions/Errors**
- **Sql queries and all its bindings**
- **Url queries and all its arguments**
- **Database connections**

## Why?

Storing user tracking information, on indexed and normalized database tables, wastes less disk space and ease the extract of valuable information about your application and business.

## Usage

As soon as you install and enable it, Tracker will start storing all information you tell it to, then you can in your application use the Tracker Facade to access everything. Here are some of the methods and relatioships available:

#### Current Session/Visitor

```
$visitor = Tracker::currentSession();
```

Most of those methods return an Eloquent model or collection, so you can use not only its attributes, but also relational data:

```
var_dump( $visitor->client_ip );

var_dump( $visitor->device->is_mobile );

var_dump( $visitor->device->platform );

var_dump( $visitor->geoIp->city );
```

#### Sessions (visits)

```
$sessions = Tracker::sessions(60 * 24); // get sessions (visits) from the past day
```

```
foreach($sessions as $session)
{
    var_dump( $session->user->email );

    var_dump( $session->device->kind . ' - ' . $session->device->platform );

    var_dump( $session->agent->browser . ' - ' . $session->agent->browser_version );

    var_dump( $session->geoIp->country_name );

    foreach($session->session->log as $log)
    {
    	var_dump( $log->path );
    }
}
```

#### Users

```
$users = Tracker::users(60 * 24);
```

#### Events

```
$events = Tracker::events(60 * 24);
```

#### Errors

```
$errors = Tracker::errors(60 * 24);
```

#### PageViews summary

```
$pageViews = Tracker::pageViews(60 * 24 * 30);
```

#### PageViews By Country summary

```
$pageViews = Tracker::pageViewsByCountry(60 * 24);
```

## Examples of the information this package may provide

### Visits

![visits](https://raw.githubusercontent.com/antonioribeiro/acr.com/master/public/assets/layouts/admin/img/screenshots/visits.png)

### Charts

![charts](https://raw.githubusercontent.com/antonioribeiro/acr.com/master/public/assets/layouts/admin/img/screenshots/charts.png)

### Users

![users](https://raw.githubusercontent.com/antonioribeiro/acr.com/master/public/assets/layouts/admin/img/screenshots/users.png)

### Events

![events](https://raw.githubusercontent.com/antonioribeiro/acr.com/master/public/assets/layouts/admin/img/screenshots/events.png)

### Errors

![errors](https://raw.githubusercontent.com/antonioribeiro/acr.com/master/public/assets/layouts/admin/img/screenshots/errors.png)

## Views

The views above are not available in this package, only the class methods used to gather this information is provided, but I'm planning to publish them in another repository.

But they exist! If you would like to use them, the way they are now, here's a gist with enough information to get them: [Laravel Stats Tracker Views](https://gist.github.com/antonioribeiro/223f661d012b458ab13f) 

## Tables

All tables are prefixed by `tracker_`, and here's an extract of some of them, showing columns and contents:

### sessions

```
+-----+--------------------------------------+---------+-----------+----------+-----------------+------------+-----------+----------+
| id  | uuid                                 | user_id | device_id | agent_id | client_ip       | referer_id | cookie_id | geoip_id |
+-----+--------------------------------------+---------+-----------+----------+-----------------+------------+-----------+----------+
| 1   | 09465be3-5930-4581-8711-5161f62c4373 | 1       | 1         | 1        | 186.228.127.245 | 2          | 1         | 2        |
| 2   | 07399969-0a19-47f0-862d-43b06d7cde45 |         | 2         | 2        | 66.240.192.138  |            | 2         | 2        |
+-----+--------------------------------------+---------+-----------+----------+-----------------+------------+-----------+----------+
```

### devices

```
+----+----------+-------------+-------------+------------------+-----------+
| id | kind     | model       | platform    | platform_version | is_mobile |
+----+----------+-------------+-------------+------------------+-----------+
| 1  | Computer | unavailable | Windows 8   |                  |           |
| 2  | Tablet   | iPad        | iOS         | 7.1.1            | 1         |
| 3  | Computer | unavailable | Windows XP  |                  |           |
| 5  | Computer | unavailable | Other       |                  |           |
| 6  | Computer | unavailable | Windows 7   |                  |           |
| 7  | Computer | unavailable | Windows 8.1 |                  |           |
| 8  | Phone    | iPhone      | iOS         | 7.1              | 1         |
+----+----------+-------------+-------------+------------------+-----------+
```

### agents

```
+----+-----------------------------------------------------------------------------------------------------------------------------------------+-------------------+-----------------+
| id | name                                                                                                                                    | browser           | browser_version |
+----+-----------------------------------------------------------------------------------------------------------------------------------------+-------------------+-----------------+
| 1  | Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36                           | Chrome            | 35.0.1916       |
| 2  | Mozilla/5.0 (iPad; CPU OS 7_1_1 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) CriOS/34.0.1847.18 Mobile/11D201 Safari/9537.53 | Chrome Mobile iOS | 34.0.1847       |
| 3  | Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)                                                                                      | IE                | 6.0             |
| 4  | Python-urllib/2.6                                                                                                                       | Other             |                 |
| 5  | Other                                                                                                                                   | Other             |                 |
| 6  | Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36                           | Chrome            | 34.0.1847       |
| 7  | Mozilla/5.0 (Windows NT 6.3; rv:28.0) Gecko/20100101 Firefox/28.0                                                                       | Firefox           | 28.0            |
| 8  | Mozilla/5.0 (iPhone; CPU iPhone OS 7_1 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Version/7.0 Mobile/11D169 Safari/9537.53 | Mobile Safari     | 7.0             |
+----+-----------------------------------------------------------------------------------------------------------------------------------------+-------------------+-----------------+
```

### domains

```
+----+--------------------------+
| id | name                     |
+----+--------------------------+
| 1  | antoniocarlosribeiro.com |
+----+--------------------------+
```

### errors

```
+----+------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| id | code | message                                                                                                                                                                                                                      |
+----+------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| 1  | 404  |                                                                                                                                                                                                                              |
| 2  | 500  | Call to undefined method PragmaRX\Tracker\Tracker::sessionLog()                                                                                                                                                              |
| 3  | 500  | Trying to get property of non-object (View: /home/forge/stage.antoniocarlosribeiro.com/app/views/admin/tracker/log.blade.php)                                                                                                |
| 4  | 500  | syntax error, unexpected 'foreach' (T_FOREACH)                                                                                                                                                                               |
| 5  | 500  | Call to undefined method PragmaRX\Tracker\Tracker::pageViewsByCountry()                                                                                                                                                      |
| 6  | 500  | Class PragmaRX\Firewall\Vendor\Laravel\Artisan\Base contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (Illuminate\Console\Command::fire)                                 |
| 7  | 405  |                                                                                                                                                                                                                              |
| 8  | 500  | Trying to get property of non-object                                                                                                                                                                                         |
| 9  | 500  | Missing argument 2 for Illuminate\Database\Eloquent\Model::setAttribute(), called in /home/forge/stage.antoniocarlosribeiro.com/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php on line 2444 and defined |
+----+------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
```

### events

```
+----+------------------------------------------------+
| id | name                                           |
+----+------------------------------------------------+
| 1  | illuminate.log                                 |
| 2  | router.before                                  |
| 3  | router.matched                                 |
| 4  | auth.attempt                                   |
| 5  | auth.login                                     |
| 6  | composing: admin.tracker.index                 |
| 7  | creating: admin.tracker._partials.menu         |
| 8  | composing: admin.tracker._partials.menu        |
| 9  | creating: admin.layout                         |
| 10 | composing: admin.layout                        |
| 11 | creating: admin._partials.mainMenu             |
| 12 | composing: admin._partials.mainMenu            |
| 13 | creating: admin._partials.messages             |
| 14 | composing: admin._partials.messages            |
| 15 | creating: global._partials.google-analytics    |
| 16 | composing: global._partials.google-analytics   |
+----+------------------------------------------------+
```

### geoip

```
+----+----------+-----------+--------------+---------------+---------------------------+--------+----------------+-------------+-----------+----------+------------+----------------+
| id | latitude | longitude | country_code | country_code3 | country_name              | region | city           | postal_code | area_code | dma_code | metro_code | continent_code |
+----+----------+-----------+--------------+---------------+---------------------------+--------+----------------+-------------+-----------+----------+------------+----------------+
| 1  | 37.4192  | -122.057  | US           | USA           | United States             | CA     | Mountain View  | 94043       | 650       | 807      | 807        | NA             |
| 2  | -10      | -55       | BR           | BRA           | Brazil                    |        |                |             |           |          |            | SA             |
| 3  | 30.3909  | -86.3161  | US           | USA           | United States             | FL     | Miramar Beach  | 32550       | 850       | 686      | 686        | NA             |
| 4  | 38.65    | -90.5334  | US           | USA           | United States             | MO     | Chesterfield   | 63017       | 314       | 609      | 609        | NA             |
| 5  | 42.7257  | -84.636   | US           | USA           | United States             | MI     | Lansing        | 48917       | 517       | 551      | 551        | NA             |
| 6  | 42.8884  | -78.8761  | US           | USA           | United States             | NY     | Buffalo        | 14202       | 716       | 514      | 514        | NA             |
| 7  | 40.1545  | -75.3809  | US           | USA           | United States             | PA     | Norristown     | 19403       | 610       | 504      | 504        | NA             |
| 8  | 47.4891  | -122.291  | US           | USA           | United States             | WA     | Seattle        | 98168       | 206       | 819      | 819        | NA             |
| 9  | 33.7516  | -84.3915  | US           | USA           | United States             | GA     | Atlanta        | 30303       | 404       | 524      | 524        | NA             |
| 10 | 33.7633  | -117.794  | US           | USA           | United States             | CA     | Santa Ana      | 92705       | 714       | 803      | 803        | NA             |
| 11 | 33.4357  | -111.917  | US           | USA           | United States             | AZ     | Tempe          | 85281       | 480       | 753      | 753        | NA             |
| 12 | 40.7421  | -74.0018  | US           | USA           | United States             | NY     | New York       | 10011       | 212       | 501      | 501        | NA             |
| 13 | 28.6185  | -81.4336  | US           | USA           | United States             | FL     | Orlando        | 32810       | 407       | 534      | 534        | NA             |
| 14 | 38.6312  | -90.1922  | US           | USA           | United States             | MO     | Saint Louis    | 63101       | 314       | 609      | 609        | NA             |
| 15 | 51       | 9         | DE           | DEU           | Germany                   |        |                |             |           |          |            | EU             |
| 16 | 52.5     | 5.75      | NL           | NLD           | Netherlands               |        |                |             |           |          |            | EU             |
| 17 | 48.86    | 2.35      | FR           | FRA           | France                    |        |                |             |           |          |            | EU             |
| 18 | 60       | 100       | RU           | RUS           | Russian Federation        |        |                |             |           |          |            | EU             |
| 19 | 51.5     | -0.13     | GB           | GBR           | United Kingdom            |        |                |             |           |          |            | EU             |
| 20 | 42.8333  | 12.8333   | IT           | ITA           | Italy                     |        |                |             |           |          |            | EU             |
| 21 | 59.3333  | 18.05     | SE           | SWE           | Sweden                    | 26     | Stockholm      |             |           |          |            | EU             |
| 22 | -41      | 174       | NZ           | NZL           | New Zealand               |        |                |             |           |          |            | OC             |
| 23 | 37.57    | 126.98    | KR           | KOR           | Korea, Republic of        |        |                |             |           |          |            | AS             |
| 24 | 1.3667   | 103.8     | SG           | SGP           | Singapore                 |        |                |             |           |          |            | AS             |
| 25 | -43.5333 | 172.633   | NZ           | NZL           | New Zealand               | E9     | Christchurch   | 8023        |           |          |            | OC             |
| 26 | -27.471  | 153.024   | AU           | AUS           | Australia                 | 04     | Brisbane       |             |           |          |            | OC             |
| 27 | 26.9167  | 75.8167   | IN           | IND           | India                     | 24     | Jaipur         |             |           |          |            | AS             |
| 28 | 32       | 53        | IR           | IRN           | Iran, Islamic Republic of |        |                |             |           |          |            | AS             |
| 29 | 32.0617  | 118.778   | CN           | CHN           | China                     | 04     | Nanjing        |             |           |          |            | AS             |
| 30 | -22.9    | -47.0833  | BR           | BRA           | Brazil                    | 27     | Campinas       |             |           |          |            | SA             |
| 31 | 32.8073  | -117.132  | US           | USA           | United States             | CA     | San Diego      | 92123       | 858       | 825      | 825        | NA             |
| 32 | -22.9    | -43.2333  | BR           | BRA           | Brazil                    | 21     | Rio De Janeiro |             |           |          |            | SA             |
+----+----------+-----------+--------------+---------------+---------------------------+--------+----------------+-------------+-----------+----------+------------+----------------+
```

### log

```
+-----+------------+---------+----------+--------+---------------+---------+-----------+---------+------------+----------+
| id  | session_id | path_id | query_id | method | route_path_id | is_ajax | is_secure | is_json | wants_json | error_id |
+-----+------------+---------+----------+--------+---------------+---------+-----------+---------+------------+----------+
| 1   | 1          | 1       |          | GET    | 1             |         | 1         |         |            |          |
| 2   | 1          | 2       |          | GET    | 2             |         | 1         |         |            |          |
| 3   | 1          | 3       |          | GET    | 3             |         | 1         |         |            |          |
| 4   | 1          | 3       |          | POST   | 4             |         | 1         |         |            |          |
+-----+------------+---------+----------+--------+---------------+---------+-----------+---------+------------+----------+
```

### paths

```
+----+--------------------------------------------------------+
| id | path                                                   |
+----+--------------------------------------------------------+
| 1  | /                                                      |
| 2  | admin                                                  |
| 3  | login                                                  |
| 4  | admin/languages                                        |
| 5  | admin/tracker                                          |
| 6  | admin/pages                                            |
| 7  | jmx-console                                            |
| 8  | manager/html                                           |
| 9  | administrator                                          |
| 10 | joomla/administrator                                   |
| 11 | cms/administrator                                      |
| 12 | Joomla/administrator                                   |
| 13 | phpmyadmin                                             |
| 14 | phpMyAdmin                                             |
| 15 | mysql                                                  |
| 16 | sql                                                    |
| 17 | myadmin                                                |
| 18 | webdav                                                 |
+----+--------------------------------------------------------+
```

### route_paths

```
+----+----------+--------------------------------------------------------+
| id | route_id | path                                                   |
+----+----------+--------------------------------------------------------+
| 1  | 1        | /                                                      |
| 2  | 2        | admin                                                  |
| 3  | 3        | login                                                  |
| 4  | 4        | login                                                  |
| 5  | 5        | admin/languages                                        |
| 6  | 6        | admin/tracker                                          |
| 7  | 7        | admin/pages                                            |
+----+----------+--------------------------------------------------------+
```

### routes

```
+----+--------------------------------------+----------------------------------------------------------+
| id | name                                 | action                                                   |
+----+--------------------------------------+----------------------------------------------------------+
| 1  | home                                 | ACR\Controllers\Home@index                               |
| 2  | admin                                | ACR\Controllers\Admin\Admin@index                        |
| 3  | login.form                           | ACR\Controllers\Logon@form                               |
| 4  | login.do                             | ACR\Controllers\Logon@login                              |
| 5  | admin.languages.index                | ACR\Controllers\Admin\Languages@index                    |
| 6  | admin.tracker.index                  | ACR\Controllers\Admin\Tracker@index                      |
| 7  | admin.pages.index                    | ACR\Controllers\Admin\Pages@index                        |
| 8  | admin.tracker.log                    | ACR\Controllers\Admin\Tracker@log                        |
| 9  | technology                           | ACR\Controllers\Technology@index                         |
| 10 | technology.articles.show             | ACR\Controllers\Technology@show                          |
| 11 | language.select                      | ACR\Controllers\Language@select                          |
| 12 | admin.tracker.summary                | ACR\Controllers\Admin\Tracker@summary                    |
| 13 | admin.tracker.api.pageviews          | ACR\Controllers\Admin\Tracker@apiPageviews               |
| 14 | admin.tracker.api.pageviewsbycountry | ACR\Controllers\Admin\Tracker@apiPageviewsByCountry      |
| 15 | admin.pages.create                   | ACR\Controllers\Admin\Pages@create                       |
| 16 | api.markdown                         | ACR\Controllers\Api@markdown                             |
| 17 | admin.pages.store                    | ACR\Controllers\Admin\Pages@store                        |
| 18 | bio                                  | ACR\Controllers\StaticPages@show                         |
| 19 | logout.do                            | ACR\Controllers\Logon@logout                             |
| 20 | admin.tracker.index                  | ACR\Controllers\Admin\UsageTracker@index                 |
| 21 | admin.tracker.api.pageviewsbycountry | ACR\Controllers\Admin\UsageTracker@apiPageviewsByCountry |
| 22 | admin.tracker.api.pageviews          | ACR\Controllers\Admin\UsageTracker@apiPageviews          |
+----+--------------------------------------+----------------------------------------------------------+
```

### sql_queries                   ;

```
+----+------------------------------------------+-------------------------------------------------------------------------------------------------+-------+---------------+
| id | sha1                                     | statement                                                                                       | time  | connection_id |
+----+------------------------------------------+-------------------------------------------------------------------------------------------------+-------+---------------+
| 1  | 5aee121018ac16dbf26dbbe0cf35fd44a29a5d7e | select * from "users" where "id" = ? limit 1                                                    | 3.13  | 1             |
| 2  | 0fc3f3a722b0f9ef38e6bee44fc3fde9fb1fd1d9 | select "created_at" from "articles" where "published_at" is not null order by "created_at" desc | 1.99  | 1             |
+----+------------------------------------------+-------------------------------------------------------------------------------------------------+-------+---------------+
```

## Requirements

- Laravel 4.1+
- PHP 5.3.7+
- Package "geoip/geoip":"~1.14" (If you are planning to store Geo IP information)

## Installing

Require the `tracker` package by **executing** the following command in your command line:

    composer require "pragmarx/tracker":"~0.4"

**Or** add to your composer.json:

    "require": {
        "pragmarx/tracker": "~0.4"
    }

And execute

    composer update

Add the service provider to your app/config/app.php:

    'PragmaRX\Tracker\Vendor\Laravel\ServiceProvider',

Create the migration:

    php artisan tracker:tables

Migrate it

    php artisan migrate

Publish tracker configuration:

    php artisan config:publish pragmarx/tracker

Create the UA Parser regex file (every time you run `composer update` you must also execute this command):

    php artisan tracker:updateparser

And edit the file `app/config/packages/pragmarx/tracker/config.php` to enable Tracker.

    'enabled' => true,

Note that the logging function is disabled by default, because it may write too much data to your database, but you can enable it by changing:

    'log_enabled' => true,

If you are planning to store Geo IP information, also install the geoip package:

    composer require "geoip/geoip":"~1.14"

And make sure you don't have the PHP module installed. This is a Debian/Ubuntu example:

	sudo apt-get purge php5-geoip

## Database Connections & Query Logs

If you are planning to store your query logs, to avoid recursion while logging SQL queries, you will need to create a different database connection for it:

This is a main connection:

	'postgresql' => [
		'driver'   => 'pgsql',
		'host'     => 'localhost',
		'database' => getenv('MAIN.DATABASE_NAME'),
		'username' => getenv('MAIN.DATABASE_USER'),
		'password' => getenv('MAIN.DATABASE_PASSWORD'),
		'charset'  => 'utf8',
		'prefix'   => '',
		'schema'   => 'public',
	],

This is the tracker connection pointing to the same database:

	'tracker' => [
		'driver'   => 'pgsql',
		'host'     => 'localhost',
		'database' => getenv('MAIN.DATABASE_NAME'),
		'username' => getenv('MAIN.DATABASE_USER'),
		'password' => getenv('MAIN.DATABASE_PASSWORD'),
		'charset'  => 'utf8',
		'prefix'   => '',
		'schema'   => 'public',
	],

On your `tracker/config.php` file, set the Tracker connection to the one you created for it:

	'connection' => 'tracker',

And ignore this connection for SQL queries logging:

	'do_not_log_sql_queries_connections' => array(
		'tracker'
	),

You don't need to use a different database, but, since Tracker may generate a huge number of records, this would be a good practice.

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Tracker is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are more than welcome.
