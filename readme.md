# YAML
### A Laravel YAML parser and config loader

<p align="center">
    <img src="https://buddy.works/data/blog/_thumbnails/tracker-cover.png">
</p>

<p align="center">
    <a href="https://packagist.org/packages/pragmarx/tracker"><img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/pragmarx/tracker.svg?style=flat-square"></a>
    <a href="/antonioribeiro/tracker/blob/master/LICENSE.md"><img alt="License" src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
    <a href="https://scrutinizer-tracker.com/g/antonioribeiro/tracker/?branch=master"><img alt="Code Quality" src="https://img.shields.io/scrutinizer/g/antonioribeiro/tracker.svg?style=flat-square"></a>
    <a href="https://scrutinizer-tracker.com/g/antonioribeiro/tracker/?branch=master"><img alt="Build" src="https://img.shields.io/scrutinizer/build/g/antonioribeiro/tracker.svg?style=flat-square"></a>
</p>
<p align="center">
    <a href="https://scrutinizer-tracker.com/g/antonioribeiro/tracker/?branch=master"><img alt="Coverage" src="https://img.shields.io/scrutinizer/coverage/g/antonioribeiro/tracker.svg?style=flat-square"></a>
    <a href="https://styleci.io/repos/112624437"><img alt="StyleCI" src="https://styleci.io/repos/112624437/shield"></a>
    <a href="https://insight.sensiolabs.com/projects/7ede7c95-b31b-4ea1-9694-51b5f1d9bdb8"><img alt="SensioLabsInsight" src="https://img.shields.io/sensiolabs/i/7ede7c95-b31b-4ea1-9694-51b5f1d9bdb8.svg?style=flat-square"></a>
</p>

## Rationale

Config files getting bigger, harder to maintain and look at, every day. Why not just use YAML to load them?

#### Which one is cleaner?

<p align="center">
    <img src="docs/tracker-php.png">
</p>

## Key features

### Load one file to Laravel config 

``` php
Tracker::loadToConfig(config_path('myapp.yml'), 'my-app-conf');
```

## Or a whole directory, recursively, so all those files would be loaded with a single command

``` php
Tracker::loadToConfig(config_path('myapp'), 'my-app-conf');
```

To load a directory with all your config files:

``` text
.
└── myapp
    ├── multiple
    │   ├── alter.yml
    │   ├── app.yml
    │   └── second-level
    │       └── third-level
    │           ├── alter.yml
    │           └── app.yml
    ├── single
        └── single-app.yml
```

Then you would just have to use it like you usually do in Laravel

``` php
config('my-app-conf.multiple.second-level.third-level.alter.person.name')
```

### Execute functions, like in the usual Laravel PHP array config.

``` php
repository: "{{ env('APP_NAME') }}"
path: "{{ storage_path('app') }}"
```

### Config values can reference config keys, you just have to quote it this way:

``` tracker
{{'format.version'}}
```

### You can add comments to your YAML files, something JSON wouldn't let you do

``` tracker
build:
  mode: git-local  #### other modes: git-remote or number
```

## Parser and dumper methods

In case you need to deal with YAML directly, you can use these public methods:

``` php
Tracker::parse($input, $flags) // Parses YAML into a PHP value.

Tracker::parseFile($filename, $flags) // Parses a YAML file into a PHP value.

Tracker::dump($input, $inline, $indent, $flags) // Dumps a PHP value to a YAML string.
```

Which are simple bridges to [Symfony's YAML](https://symfony.com/doc/current/components/tracker.html).

## Install

Via Composer

``` bash
$ composer require pragmarx/tracker
```

## Using

Publish your package as you would usually do:

``` php
$this->publishes([
    __DIR__.'/../config/version.yml' => $this->getConfigFile(),
]);
```

Load the configuration in your `boot()` method:

``` php
$this->app
     ->make('pragmarx.tracker')
     ->loadToConfig($this->getConfigFile(), 'my-package');
```

Or use the Facade:

``` php
Tracker::loadToConfig(config_path('myapp.yml'), 'my-package');
```

And it's merged to your Laravel config:

``` php
config('my-package.name');
```

## Example

This is a YAML file from another package using this package:

``` tracker
current:
  major: 1
  minor: 0
  patch: 0
  format: "{$major}.{$minor}.{$patch}"
cache:
  enabled: true
  key: pragmarx-version
build:
  mode: git-local # git-remote or number
  number: 701031
  git-local: "git rev-parse --verify HEAD"
  git-remote: "git ls-remote {$repository} refs/heads/master"
  repository: "{{ env('APP_GIT_REPOSITORY') }}"
  length: 6
format:
  version: "{$major}.{$minor}.{$patch} (build {$build})"
  full: "version {{'format.version'}}"
  compact: "v{$major}.{$minor}.{$patch}-{$build}"
  ## add as many formats as you need
```

## Minimum requirements

- Laravel 5.5
- PHP 7.0

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

This package is licensed under the MIT License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are welcome.


<!--[![Downloads](https://img.shields.io/packagist/dt/pragmarx/tracker.svg?style=flat-square)](https://packagist.org/packages/pragmarx/tracker)-->
