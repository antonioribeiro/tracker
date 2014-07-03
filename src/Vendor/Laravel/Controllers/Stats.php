<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Controllers;


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
			->with('sessions', Tracker::sessions(60 * 24 * Session::get('tracker.stats.days')))
			->with('title', 'Visits')
			->with('username_column', Tracker::getConfig('authenticated_user_username_column'));
	}

	public function log($uuid)
	{
		return View::make('pragmarx/tracker::log')
				->with('log', Tracker::sessionLog($uuid))
				->with('title', 'Log');
	}

	public function summary()
	{
		return View::make('pragmarx/tracker::summary')
				->with('title', 'Page Views Summary');
	}

	public function apiPageviews()
	{
		return Tracker::pageViews(
			60 * 24 * Session::get('tracker.stats.days')
		)->toJson();
	}

	public function apiPageviewsByCountry()
	{
		return Tracker::pageViewsByCountry(
			60 * 24 * Session::get('tracker.stats.days')
		)->toJson();
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
			->with('users', Tracker::users(60 * 24 * Session::get('tracker.stats.days')))
			->with('title', 'Users')
			->with('username_column', Tracker::getConfig('authenticated_user_username_column'));
	}

	private function events()
	{
		return View::make('pragmarx/tracker::events')
			->with('events', Tracker::events(60 * 24 * Session::get('tracker.stats.days')))
			->with('title', 'Events');
	}

	public function errors()
	{
		return View::make('pragmarx/tracker::errors')
			->with('error_log', Tracker::errors(60 * 24 * Session::get('tracker.stats.days')))
			->with('title', 'Errors');
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
