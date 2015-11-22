<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Controllers;

use Auth;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use PragmaRX\Tracker\Support\Minutes;
use Illuminate\Support\Facades\Input;
use Bllim\Datatables\Facade\Datatables;
use Illuminate\Support\Facades\Session;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;

class Stats extends Controller
{
    private $adminProperties = [
        'is_admin',
        'is_root',
    ];

	public function __construct()
	{
		Session::put('tracker.stats.days', $this->getValue('days', 1));

		Session::put('tracker.stats.page', $this->getValue('page', 'visits'));

		$this->minutes = new Minutes(60 * 24 * Session::get('tracker.stats.days'));

        $this->authentication = app()->make('tracker.authentication');
	}

	public function index()
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

		return $this->showPage(Session::get('tracker.stats.page'));
	}

	public function showPage($page)
	{
		$me = $this;

		if (method_exists($me, $page))
		{
			return $this->$page();
		}
	}

	public function visits()
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
                { "data" : "referer",     "title" : "Referer", "orderable": true, "searchable": true },
                { "data" : "pageViews",   "title" : "Page Views", "orderable": true, "searchable": true },
                { "data" : "lastActivity","title" : "Last Activity", "orderable": true, "searchable": true },
            '
		);

		return View::make('pragmarx/tracker::index')
			->with('sessions', Tracker::sessions($this->minutes))
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

	public function apiPageviews()
	{
		return Tracker::pageViews($this->minutes)->toJson();
	}

	public function apiPageviewsByCountry()
	{
		return Tracker::pageViewsByCountry($this->minutes)->toJson();
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

	public function getValue($variable, $default = null)
	{
		if (Input::has($variable))
		{
			$value = Input::get($variable);
		}
		else
		{
			$value = Session::get('tracker.stats.'.$variable, $default);
		}

		return $value;
	}

	public function users()
	{
		return View::make('pragmarx/tracker::users')
			->with('users', Tracker::users($this->minutes))
			->with('title', 'Users')
			->with('username_column', Tracker::getConfig('authenticated_user_username_column'));
	}

	private function events()
	{
		return View::make('pragmarx/tracker::events')
			->with('events', Tracker::events($this->minutes))
			->with('title', 'Events');
	}

	public function errors()
	{
		return View::make('pragmarx/tracker::errors')
			->with('error_log', Tracker::errors($this->minutes))
			->with('title', 'Errors');
	}

	public function apiErrors()
	{
		$query = Tracker::errors($this->minutes, false);

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

	public function apiEvents()
	{
		$query = Tracker::events($this->minutes, false);

		return Datatables::of($query)->make(true);
	}

	public function apiUsers()
	{
		$username_column = Tracker::getConfig('authenticated_user_username_column');

		return Datatables::of(Tracker::users($this->minutes, false))
				->edit_column('user_id', function($row) use ($username_column) {
					return "{$row->user->$username_column}";
				})
				->edit_column('updated_at', function($row) {
					return "{$row->updated_at->diffForHumans()}";
				})
				->make(true);
	}

	public function apiVisits()
	{
		$username_column = Tracker::getConfig('authenticated_user_username_column');

		$query = Tracker::sessions($this->minutes, false);

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

