<?php

namespace PragmaRX\Tracker\Support\Database;

use PragmaRX\Support\Migration;

class Migrator extends Migration {

	protected $tables = array(
		'tracker_errors',
		'tracker_sessions',
		'tracker_referers',
		'tracker_domains',
		'tracker_routes',
		'tracker_route_paths',
		'tracker_route_path_parameters',
		'tracker_devices',
		'tracker_cookies',
		'tracker_agents',
		'tracker_query_arguments',
		'tracker_queries',
		'tracker_paths',
		'tracker_log',
		'tracker_geoip',
		'tracker_sql_queries',
		'tracker_sql_queries_log',
		'tracker_sql_query_bindings',
		'tracker_sql_query_bindings_parameters',
		'tracker_connections',
		'tracker_events',
		'tracker_events_log',
		'tracker_system_classes',
	);

	protected function migrateUp()
	{
		$this->builder->create(
			'tracker_log',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('session_id')->unsigned()->index();
				$table->bigInteger('path_id')->unsigned()->nullable()->index();
				$table->bigInteger('query_id')->unsigned()->nullable()->index();
				$table->string('method', 10)->index();
				$table->bigInteger('route_path_id')->unsigned()->nullable()->index();
				$table->boolean('is_ajax');
				$table->boolean('is_secure');
				$table->boolean('is_json');
				$table->boolean('wants_json');
				$table->bigInteger('error_id')->unsigned()->nullable()->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_paths',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('path')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_queries',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('query')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_query_arguments',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('query_id')->unsigned()->index();
				$table->string('argument')->index();
				$table->string('value')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_routes',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('name')->index();
				$table->string('action')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_route_paths',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('route_id')->index();
				$table->string('path')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_route_path_parameters',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('route_path_id')->unsigned()->index();
				$table->string('parameter')->index();
				$table->string('value')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_agents',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('name')->unique();
				$table->string('browser')->index();
				$table->string('browser_version');

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_cookies',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('uuid')->unique();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_devices',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('kind', 16)->index();
				$table->string('model', 64)->index();
				$table->string('platform', 64)->index();
				$table->string('platform_version', 16)->index();
				$table->boolean('is_mobile');

				$table->unique(['kind', 'model', 'platform', 'platform_version']);

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_referers',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('domain_id')->unsigned()->index();
				$table->string('url')->index();
				$table->string('host');

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_domains',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('name')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_sessions',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('uuid')->unique()->index();
				$table->bigInteger('user_id')->unsigned()->nullable()->index();
				$table->bigInteger('device_id')->unsigned()->nullable()->index();
				$table->bigInteger('agent_id')->unsigned()->nullable()->index();
				$table->string('client_ip')->index();
				$table->bigInteger('referer_id')->unsigned()->nullable()->index();
				$table->bigInteger('cookie_id')->unsigned()->nullable()->index();
				$table->bigInteger('geoip_id')->unsigned()->nullable()->index();
				$table->boolean('is_robot');

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_errors',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('code')->index();
				$table->string('message')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_geoip',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->double('latitude')->nullable()->index();
				$table->double('longitude')->nullable()->index();

				$table->string('country_code', 2)->nullable()->index();
				$table->string('country_code3', 3)->nullable()->index();
				$table->string('country_name')->nullable()->index();
				$table->string('region', 2)->nullable();
				$table->string('city', 50)->nullable()->index();
				$table->string('postal_code', 20)->nullable();
				$table->bigInteger('area_code')->nullable();
				$table->double('dma_code')->nullable();
				$table->double('metro_code')->nullable();
				$table->string('continent_code', 2)->nullable();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_sql_queries',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('sha1', 40)->index();
				$table->text('statement');
				$table->double('time')->index();
				$table->integer('connection_id')->unsigned();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_sql_query_bindings',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('sha1', 40)->index();
				$table->text('serialized');

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_sql_query_bindings_parameters',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('sql_query_bindings_id')->unsigned()->nullable();
				$table->string('name')->nullable()->index();
				$table->text('value')->nullable();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_sql_queries_log',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('log_id')->unsigned()->index();
				$table->bigInteger('sql_query_id')->unsigned()->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_connections',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('name')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_events',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('name')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_events_log',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->bigInteger('event_id')->unsigned()->index();
				$table->bigInteger('class_id')->unsigned()->nullable()->index();
				$table->bigInteger('log_id')->unsigned()->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);

		$this->builder->create(
			'tracker_system_classes',
			function ($table)
			{
				$table->bigIncrements('id');

				$table->string('name')->index();

				$table->timestamp('created_at')->index();
				$table->timestamp('updated_at')->index();
			}
		);
	}

	protected function migrateDown()
	{
		$this->dropAllTables();
	}
}
