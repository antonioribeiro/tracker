<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Controllers;

use Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Bllim\Datatables\Facade\Datatables;
use PragmaRX\Tracker\Vendor\Laravel\Support\Session;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;

class Stats extends Controller
{
    private $adminProperties = [
        'admin',
        'root',
        'is_admin',
        'is_root',
    ];

	public function __construct()
	{
        $this->authentication = app()->make('tracker.authentication');
	}

	public function index(Session $session)
	{
        if ( ! $this->isAuthenticated())
        {
            return View::make('pragmarx/tracker::message')->with('message', 'Authentication required');
        }

        if ( ! $this->hasAdminProperty())
        {
            return View::make('pragmarx/tracker::message')->with('message', 'User model misses admin property');
        }

        if ( ! $this->isAdmin())
        {
            return View::make('pragmarx/tracker::message')->with('message', 'You are not Admin');
        }

		return $this->showPage($session, $session->getValue('page'));
	}

	public function showPage($session, $page)
	{
		$me = $this;

		if (method_exists($me, $page))
		{
			return $this->$page($session);
		}
	}

	public function visits(Session $session)
	{
		$datatables_data = array
		(
			'datatables_ajax_route' => route('tracker.stats.api.visits'),
			'datatables_columns' =>
				'
                { "data" : "id",          "title" : "Id", "orderable": true, "searchable": true },
                { "data" : "client_ip",   "title" : "IP Address", "orderable": true, "searchable": true },
                { "data" : "country",     "title" : "Country / City", "orderable": true, "searchable": true },
                { "data" : "user",        "title" : "User", "orderable": true, "searchable": true },
                { "data" : "device",      "title" : "Device", "orderable": true, "searchable": true },
                { "data" : "browser",     "title" : "Browser", "orderable": true, "searchable": true },
                { "data" : "language",    "title" : "Language", "orderable": true, "searchable": true },
                { "data" : "referer",     "title" : "Referer", "orderable": true, "searchable": true },
                { "data" : "pageViews",   "title" : "Page Views", "orderable": true, "searchable": true },
                { "data" : "lastActivity","title" : "Last Activity", "orderable": true, "searchable": true },
            '
		);

		return View::make('pragmarx/tracker::index')
			->with('sessions', Tracker::sessions($session->getMinutes()))
			->with('title', 'Visits')
			->with('username_column', Tracker::getConfig('authenticated_user_username_column'))
			->with('datatables_data', $datatables_data);
	}

	public function log($uuid)
	{
		return View::make('pragmarx/tracker::log')
				->with('log', Tracker::sessionLog($uuid))
				->with('uuid', $uuid)
				->with('title', 'Log');
	}

	public function summary()
	{
		return View::make('pragmarx/tracker::summary')
				->with('title', 'Page Views Summary');
	}

	public function apiPageviews(Session $session)
	{
		return Tracker::pageViews($session->getMinutes())->toJson();
	}

	public function apiPageviewsByCountry(Session $session)
	{
		return Tracker::pageViewsByCountry($session->getMinutes())->toJson();
	}

	public function apiLog($uuid)
	{
		$query = Tracker::sessionLog($uuid, false);

		$query->select(array(
			               'id',
			               'session_id',
			               'method',
			               'path_id',
			               'query_id',
			               'route_path_id',
			               'is_ajax',
			               'is_secure',
			               'is_json',
			               'wants_json',
			               'error_id',
			               'updated_at',
		               ));

		return Datatables::of($query)
			->edit_column('route_name', function($row) {
					return 	$row->routePath
							? $row->routePath->route->name . '<br>' . $row->routePath->route->action
							: ($row->path ? $row->path->path : '');
			})

			->edit_column('route', function($row) {
				$route = null;

				if ($row->routePath)
				{
					foreach ($row->routePath->parameters as $parameter)
					{
						$route .= ($route ? '<br>' : '') . $parameter->parameter . '=' . $parameter->value;
					}
				}

				return $route;
			})

			->edit_column('query', function($row) {
				$query = null;

				if ($row->logQuery)
				{
					foreach ($row->logQuery->arguments as $argument)
					{
						$query .= ($query ? '<br>' : '') . $argument->argument . '=' . $argument->value;
					}
				}

				return $query;
			})

			->edit_column('is_ajax', function($row) {
				return 	$row->is_ajax ? 'yes' : '';
			})

			->edit_column('is_secure', function($row) {
				return 	$row->is_secure ? 'yes' : '';
			})

			->edit_column('is_json', function($row) {
				return 	$row->is_json ? 'yes' : '';
			})

			->edit_column('wants_json', function($row) {
				return 	$row->wants_json ? 'yes' : '';
			})

			->edit_column('error', function($row) {
				return 	$row->error ? 'yes' : '';
			})

			->make(true);
	}


	public function users(Session $session)
	{
		return View::make('pragmarx/tracker::users')
			->with('users', Tracker::users($session->getMinutes()))
			->with('title', 'Users')
			->with('username_column', Tracker::getConfig('authenticated_user_username_column'));
	}

	private function events(Session $session)
	{
		return View::make('pragmarx/tracker::events')
			->with('events', Tracker::events($session->getMinutes()))
			->with('title', 'Events');
	}

	public function errors(Session $session)
	{
		return View::make('pragmarx/tracker::errors')
			->with('error_log', Tracker::errors($session->getMinutes()))
			->with('title', 'Errors');
	}

	public function apiErrors(Session $session)
	{
		$query = Tracker::errors($session->getMinutes(), false);

		$query->select(array(
			               'id',
			               'error_id',
			               'session_id',
			               'path_id',
			               'updated_at',
		               ));

		return Datatables::of($query)
				->edit_column('updated_at', function($row) {
					return "{$row->updated_at->diffForHumans()}";
				})
				->make(true);
	}

	public function apiEvents(Session $session)
	{
		$query = Tracker::events($session->getMinutes(), false);

		return Datatables::of($query)->make(true);
	}

	public function apiUsers(Session $session)
	{
		$username_column = Tracker::getConfig('authenticated_user_username_column');

		return Datatables::of(Tracker::users($session->getMinutes(), false))
				->edit_column('user_id', function($row) use ($username_column) {
					return "{$row->user->$username_column}";
				})
				->edit_column('updated_at', function($row) {
					return "{$row->updated_at->diffForHumans()}";
				})
				->make(true);
	}

	public function apiVisits(Session $session)
	{
		$username_column = Tracker::getConfig('authenticated_user_username_column');

		$query = Tracker::sessions($session->getMinutes(), false);

		$query->select(array(
               'id',
               'uuid',
               'user_id',
               'device_id',
               'agent_id',
               'client_ip',
               'referer_id',
               'cookie_id',
               'geoip_id',
               'language_id',
               'is_robot',
               'updated_at',
		));

		return Datatables::of($query)
				->edit_column('id', function($row) use ($username_column)
				{
					$uri = route('tracker.stats.log', $row->uuid);

					return '<a href="'.$uri.'">'.$row->id.'</a>';
				})

				->add_column('country', function ($row)
				{
					$cityName = $row->geoip && $row->geoip->city ? ' - '.$row->geoip->city : '';

					$countryName = ($row->geoip ? $row->geoip->country_name : '') . $cityName;

					$countryCode = strtolower($row->geoip ? $row->geoip->country_code : '');

					$flag = $countryCode
							? "<span class=\"f16\"><span class=\"flag $countryCode\" alt=\"$countryName\" /></span></span>"
							: '';

					return "$flag $countryName";
				})

				->add_column('user', function($row) use ($username_column)
				{
					return $row->user ? $row->user->$username_column : 'guest';
				})

				->add_column('device', function($row) use ($username_column)
				{
					$model = ($row->device && $row->device->model && $row->device->model !== 'unavailable' ? '['.$row->device->model.']' : '');

					$platform = ($row->device && $row->device->platform ? ' ['.trim($row->device->platform.' '.$row->device->platform_version).']' : '');

					$mobile = ($row->device && $row->device->is_mobile ? ' [mobile device]' : '');

					return $model || $platform || $mobile
							? $row->device->kind . ' ' . $model . ' ' . $platform . ' ' . $mobile
							: '';
				})

				->add_column('browser', function($row) use ($username_column)
				{
					return $row->agent && $row->agent
							? $row->agent->browser . ' ('.$row->agent->browser_version.')'
							: '';

				})

                ->add_column('language', function($row) use ($username_column)
                {
                    return $row->language && $row->language
                        ? $row->language->preference
                        : '';
                })

				->add_column('referer', function($row) use ($username_column)
				{
					return $row->referer ? $row->referer->domain->name : '';
				})

				->add_column('pageViews', function($row) use ($username_column)
				{
					return $row->page_views;
				})

				->add_column('lastActivity', function($row) use ($username_column)
				{
					return $row->updated_at->diffForHumans();
				})

				->make(true);
	}

    private function isAuthenticated()
    {
        return $this->authentication->check();
    }

    private function hasAdminProperty()
    {
        $user = $this->authentication->user();

        foreach ($this->adminProperties as $property)
        {
            $propertyCamel = camel_case($property);

            if (
                    isset($user->$property) ||
                    isset($user->$propertyCamel) ||
                    method_exists($user, $property) ||
                    method_exists($user, $propertyCamel)
            )
            {
                return true;
            }
        }

        return false;
    }

    private function isAdmin()
    {
        $user = $this->authentication->user();

        foreach ($this->adminProperties as $property)
        {
            $propertyCamel = camel_case($property);

            if (
                (isset($user->$property) && $user->$property) ||
                (isset($user->$propertyCamel) && $user->$propertyCamel) ||
                (method_exists($user, $property) && $user->$property()) ||
                (method_exists($user, $propertyCamel) && $user->$propertyCamel())
            )
            {
                return true;
            }
        }

        return false;
    }
}

