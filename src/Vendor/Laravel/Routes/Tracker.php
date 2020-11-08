<?php
namespace PragmaRX\Tracker\Vendor\Laravel\Controllers;

Class Tracker {

protected function loadRoutes()
    {
        if (!config('tracker.stats_panel_enabled')) {
            return false;
        }

        $prefix = config('tracker.stats_base_uri');

        $namespace = config('tracker.stats_controllers_namespace');

        $filters = [];

        if ($before = config('tracker.stats_routes_before_filter')) {
            $filters['before'] = $before;
        }

        if ($after = config('tracker.stats_routes_after_filter')) {
            $filters['after'] = $after;
        }

        if ($middleware = config('tracker.stats_routes_middleware')) {
            $filters['middleware'] = $middleware;
        }

        $router = app()->make('router');
        $router->group(['namespace' => $namespace], function () use ($prefix, $router, $filters) {
            $router->group($filters, function () use ($prefix, $router) {
                $router->group(['prefix' => $prefix], function ($router) {
                    $router->get('/', ['as' => 'tracker.stats.index', 'uses' => 'Stats@index']);

                    $router->get('log/{uuid}', ['as' => 'tracker.stats.log', 'uses' => 'Stats@log']);

                    $router->get('api/pageviews', ['as' => 'tracker.stats.api.pageviews', 'uses' => 'Stats@apiPageviews']);

                    $router->get('api/pageviewsbycountry', ['as' => 'tracker.stats.api.pageviewsbycountry', 'uses' => 'Stats@apiPageviewsByCountry']);

                    $router->get('api/log/{uuid}', ['as' => 'tracker.stats.api.log', 'uses' => 'Stats@apiLog']);

                    $router->get('api/errors', ['as' => 'tracker.stats.api.errors', 'uses' => 'Stats@apiErrors']);

                    $router->get('api/events', ['as' => 'tracker.stats.api.events', 'uses' => 'Stats@apiEvents']);

                    $router->get('api/users', ['as' => 'tracker.stats.api.users', 'uses' => 'Stats@apiUsers']);

                    $router->get('api/visits', ['as' => 'tracker.stats.api.visits', 'uses' => 'Stats@apiVisits']);
                });
            });
        });
    }


}
