# Laravel Tracker Upgrading guide

## to 0.3.2

- Add a is_robot boolean column to: `ALTER TABLE tracker_sessions ADD is_robot BOOL;`
- Add `'do_not_track_robots' => true or false,` to tracker\config.php

