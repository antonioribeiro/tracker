# Changelog

## [2.0.3] - 2016-08-22
### Fixed
- Routes for stats are now being correctly ignored on Laravel 5.x

## [2.0.2] - 2016-08-20
### Add
- Support for Laravel 5.3
- Middleware for Laravel 5.x
- Allow table to be prefixed
### Fixed
- SB Admin 2 install instructions

## [2.0.1] - 2016-08-18
### Changed
- Upgraded Ramsey UUID to V3

## [2.0.0] - 2015-11-23
### Notes ! This is a breaking change
- You must execute
    php artisan tracker:tables
  then
    php artisan migrate
### Added
- Referer to tracker_log
- Method Tracker::userDevices()
- Range filter on data access methods
### Fixed
- Prevent migrations files from being overwritten

## [1.0.8] - 2015-11-23
### Fixed
- Event log error when opening stats
### Added
- Must be admin to access stats

## [1.0.7] - 2015-11-22
### Added
- Support for GeoIp2
### Changed
- Upgraded SB Admin 2 to 1.0.7

## [1.0.6] - 2015-11-21
### Added
- Referer parsing, to store marketing attribution data (medium, source and search term)
- Tracker::trackVisit()
- Tracker::trackEvent()
### Changed
- Move to UA-PHP instead of PragmaRX/UaParser
- Using a better bot detector 
### Fixed
- Correctly get the application url
- Migrations for MySQL
- Sessions now change when a different users logs in
- isPhone compatibility
### Changed
- Use ua-php instead of ua-parser directly
- No need to execute tracker:updateparser during install anymore

## [1.0.5] - 2015-03-10
### Added
- Middleware filter to routes

## [1.0.4] - 2015-03-10
### Fixed
- Console exception handler

## [1.0.3] - 2015-03-08
### Fixed
- Datatables bug

## [1.0.2] - 2015-03-07
### Fixed
- Migrations for MySQL

## [1.0.1] - 2015-03-06
### Changed
- Use a stable version of datatables

## [1.0.0] - 2015-02-21
### Changed
- Added support for Laravel 5

## [0.5.2] - 2014-07-06
### Fixed
- HTTP cache by removing PHP session_start

## [0.5.1] - 2014-07-03
###Added
- A 'Today' filter option on Stats Panel
- All stats tables are now Google Charts Tables (paginated)

## [0.5.0] - 2014-07-02
###Changed
- Moved pie charts from flot to Google Charts
 
