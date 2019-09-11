# Changelog

## [3.4.1] - 2019-09-11
### Fixed
- Removed support for Laravel 4

## [3.4.0] - 2019-09-10
### New
- Added support for Laravel 6

## [3.3] - 2018-03-19
### Fixed
- Laravel 5.6 compatibility

## [3.1.8] - 2017-07-31
### New
- Added a config to enable/disable console logging

## [3.1.7] - 2017-07-31
### New
- Disable console logging

## [3.1.6] - 2017-07-31
### Fixed
- Fix wrong date column being used in log view

## [3.1.5] - 2017-07-30
### Fixed
- Fix multiple sessions for the same request

## [3.1.4] - 2017-06-25
### Fixed
- Browser Agent name is now being hashed

## [3.1.3] - 2017-06-20
### New
- Show untrackable items in log


## [3.1.2] - 2017-01-31
### New
- Upgraded datatables

## [3.1.1] - 2017-01-31
### New
- Upgraded to SBAdmin 2 3.3.7+1


## [3.1.0] - 2017-01-31
### New
- Upgrade to Laravel 5.4

## [3.0.0] - 2016-08-24
### New
- Cache

## [2.0.9] - 2016-08-23
### Added
- Support for multiple authentication drivers
### Fixed
- Migrations for languages
- Query log for Laravel 5.2 and Laravel 5.3

## [2.0.6] - 2016-08-23
### Added
- Changed to PSR-2 using StyleCI

## [2.0.5] - 2016-08-23
### Added
- Add onlineUsers method

## [2.0.4] - 2016-08-22
### Fixed
- Route name on log
- Sessions for tracking visitors
### Added
- Log languages (please check upgrade.md)
- Localization

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
### Added
- A 'Today' filter option on Stats Panel
- All stats tables are now Google Charts Tables (paginated)

## [0.5.0] - 2014-07-02
### Changed
- Moved pie charts from flot to Google Charts
 
