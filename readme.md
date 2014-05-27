# Laravel Tracker

[![Latest Stable Version](https://poser.pugx.org/pragmarx/zipcode/v/stable.png)](https://packagist.org/packages/pragmarx/zipcode) [![License](https://poser.pugx.org/pragmarx/zipcode/license.png)](https://packagist.org/packages/pragmarx/zipcode)

## A Laravel User Tracker/Logger package

Tracker gathers information from your requests to store identify:

- Devices
- Browsers and its versions
- Operating Systems and its versions

It also has tha ability to log your site accesses, by recording:

- Session
- User
- User device (by keeping a cookie on each device)
- Routes and all its parameters
- Queries and all its arguments
- Referer
- Errors

## Tables

Here's a view of tables contents after

## Requirements

- Laravel 4.1+
- PHP 5.3.7+

## Installing

Require the `zipcode` package by **executing** the following command in your command line:

    composer require "pragmarx/zipcode" "~1.0"

**Or** add to your composer.json:

    "require": {
        "pragmarx/zipcode": "~1.0"
    }

And execute

    composer update

Add the service provider to your app/config/app.php:

    'PragmaRX\ZIPcode\Vendor\Laravel\ServiceProvider',

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

ZIPcode is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are more than welcome.
