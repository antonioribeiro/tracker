<?php

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Support\Config;

class Route extends Repository
{
    public function __construct($model, Config $config)
    {
        parent::__construct($model);

        $this->config = $config;
    }

    public function isTrackable($route)
    {
        $forbidden = $this->config->get('do_not_track_routes');

        return
            !$forbidden ||
            !$route->currentRouteName() ||
            !in_array_wildcard($route->currentRouteName(), $forbidden);
    }

    public function pathIsTrackable($path)
    {
        $forbidden = $this->config->get('do_not_track_paths');

        return
            !$forbidden ||
            empty($path) ||
            !in_array_wildcard($path, $forbidden);
    }
}
