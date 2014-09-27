<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Controllers;


use Bllim\Datatables\Facade\Datatables;
use Illuminate\Support\Facades\Response;
use PragmaRX\Tracker\Support\Minutes;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class Stats extends Controller {

	public function __construct()
	{
		Session::put('tracker.stats.days', $this->getValue('days', 1));

		Session::put('tracker.stats.page', $this->getValue('page', 'visits'));

		$this->minutes = new Minutes(60 * 24 * Session::get('tracker.stats.days'));

		$this->buildComposers();
	}

	public function index()
	{
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
		return View::make('pragmarx/tracker::index')
			->with('sessions', Tracker::sessions($this->minutes))
			->with('title', 'Visits')
			->with('username_column', Tracker::getConfig('authenticated_user_username_column'));
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
		$columns = array(
			array('type' => 'string', 'label' => 'Method'),
			array('type' => 'string', 'label' => 'Route Name / Action'),
			array('type' => 'string', 'label' => 'Route'),
			array('type' => 'string', 'label' => 'Query'),
			array('type' => 'string', 'label' => 'Is ajax?'),
			array('type' => 'string', 'label' => 'Is secure?'),
			array('type' => 'string', 'label' => 'Is json?'),
			array('type' => 'string', 'label' => 'Wants Json?'),
			array('type' => 'string', 'label' => 'Error?'),
			array('type' => 'datetime', 'label' => 'Created at'),
		);

		$data = array();

		foreach(Tracker::sessionLog($uuid) as $row)
		{
			$query = null;

			if ($row->logQuery)
			{
				foreach($row->logQuery->arguments as $argument)
				{
					$query .= ($query ? '<br>' : '') . $argument->argument . '=' . $argument->value;
				}
			}

			$route = null;

			if ($row->routePath)
			{
				foreach($row->routePath->parameters as $parameter)
				{
					$route .= ($route ? '<br>' : '') . $parameter->parameter . '=' . $parameter->value;
				}
			}

			$data[] = [
				$row->method,
				$row->routePath ? $row->routePath->route->name . '<br>' . $row->routePath->route->action : $row->path->path,
				$route,
				$query,
				$row->is_ajax ? 'yes' : '',
				$row->is_secure ? 'yes' : '',
				$row->is_json ? 'yes' : '',
				$row->wants_json ? 'yes' : '',
				$row->error ? 'yes' : '',
				(string) $row->created_at,
			];
		}

		return Response::json(array(
			'columns' => $columns,
		    'data' => $data,
		));
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
					return link_to_route('tracker.stats.log', $row->id, ['uuid' => $row->uuid]);
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
					$model = ($row->device->model && $row->device->model !== 'unavailable' ? '['.$row->device->model.']' : '');

					$platform = ($row->device->platform ? ' ['.trim($row->device->platform.' '.$row->device->platform_version).']' : '');

					$mobile = ($row->device->is_mobile ? ' [mobile device]' : '');

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

	private function buildComposers()
	{
		$template_path = url('/') . Config::get('pragmarx/tracker::stats_template_path');

		View::composer('pragmarx/tracker::*', function($view) use ($template_path)
		{
			$view->with('stats_template_path', $template_path);
		});
	}

}

