# Laravel Tracker

[![Latest Stable Version](https://poser.pugx.org/pragmarx/tracker/v/stable.png)](https://packagist.org/packages/pragmarx/tracker) [![License](https://poser.pugx.org/pragmarx/tracker/license.png)](https://packagist.org/packages/pragmarx/tracker)

# THIS IS A WORK IN PROGRESS, IT WORKS BUT IS NOT DONE YET.

## A Laravel Visitor Tracker package

Tracker gathers information from your requests to identify and/or store:

- **Device** (computer, smartphone, tablet...)
- **Browser** (Chrome, Mozilla Firefox, Safari, Internet Explorer...)
- **Operating System** (iOS, Mac OS, Linux, Windows...)
- **Geo Location** (Country and City)
- **Session**
- **Logged users**
- **User device** (by keeping a cookie on each device)
- **Routes and all its parameters**
- **Url queries and all its arguments**
- **Sql queries and all its bindings**
- **Events**
- **Referers**
- **Exceptions/Errors**

## Tables

Here's a view of tables contents after

## Requirements

- Laravel 4.1+
- PHP 5.3.7+

## Installing

Require the `tracker` package by **executing** the following command in your command line:

    composer require "pragmarx/tracker":"~0.2"

**Or** add to your composer.json:

    "require": {
        "pragmarx/tracker": "~0.2"
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

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Tracker is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are more than welcome.
