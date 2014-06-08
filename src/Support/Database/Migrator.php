<?php

/**
 * Part of the Tracker package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Tracker
 * @version    1.0.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Support\Database;

use Illuminate\Database\DatabaseManager;

class Migrator
{
	private $manager;
	
	private $connection;

	public function __construct(DatabaseManager $manager, $connection)
	{
		$this->manager = $manager;

		$this->connection = $connection;
	}

	public function up()
	{
		$this->execute('createTables');
	}

	public function createTables()
	{
		$this->getSchemaBuilder()->create('tracker_log', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('session_id')->unsigned()->index();
			$table->bigInteger('path_id')->unsigned()->index();
			$table->bigInteger('query_id')->unsigned()->nullable()->index();
			$table->string('method',10)->index();
			$table->bigInteger('route_path_id')->unsigned()->nullable()->index();
			$table->boolean('is_ajax');
			$table->boolean('is_secure');
			$table->boolean('is_json');
			$table->boolean('wants_json');
			$table->bigInteger('error_id')->unsigned()->nullable()->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_paths', function($table)
		{
			$table->bigIncrements('id');

			$table->string('path')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_queries', function($table)
		{
			$table->bigIncrements('id');

			$table->string('query')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_query_arguments', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('query_id')->unsigned()->index();
			$table->string('argument')->index();
			$table->string('value')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_routes', function($table)
		{
			$table->bigIncrements('id');

			$table->string('name')->index();
			$table->string('action')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_route_paths', function($table)
		{
			$table->bigIncrements('id');

			$table->string('route_id')->index();
			$table->string('path')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_route_path_parameters', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('route_path_id')->unsigned()->index();
			$table->string('parameter')->index();
			$table->string('value')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_agents', function($table)
		{
			$table->bigIncrements('id');

			$table->string('name')->unique();
			$table->string('browser')->index();
			$table->string('browser_version');

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_cookies', function($table)
		{
			$table->bigIncrements('id');

			$table->string('uuid')->unique();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_devices', function($table)
		{
			$table->bigIncrements('id');

			$table->string('kind')->index();
			$table->string('model')->index();
			$table->string('platform')->index();
			$table->string('platform_version')->index();
			$table->boolean('is_mobile');

			$table->unique(['kind', 'model', 'platform', 'platform_version']);

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_referers', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('domain_id')->unsigned()->index();
			$table->string('url')->index();
			$table->string('host');

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_domains', function($table)
		{
			$table->bigIncrements('id');

			$table->string('name')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_sessions', function($table)
		{
			$table->bigIncrements('id');

			$table->string('uuid')->unique()->index();
			$table->bigInteger('user_id')->unsigned()->nullable()->index();
			$table->bigInteger('device_id')->unsigned()->index();
			$table->bigInteger('agent_id')->unsigned()->index();
			$table->string('client_ip')->index();
			$table->bigInteger('referer_id')->unsigned()->nullable()->index();
			$table->bigInteger('cookie_id')->unsigned()->nullable()->index();
			$table->bigInteger('geoip_id')->unsigned()->nullable()->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_errors', function($table)
		{
			$table->bigIncrements('id');

			$table->string('code')->index();
			$table->string('message')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_geoip', function($table)
		{
			$table->bigIncrements('id');

			$table->float('latitude')->nullable()->index();
			$table->float('longitude')->nullable()->index();

			$table->string('country_code', 2)->nullable()->index();
			$table->string('country_code3', 3)->nullable()->index();
			$table->string('country_name')->nullable()->index();
			$table->string('region', 2)->nullable();
			$table->string('city', 50)->nullable()->index();
			$table->string('postal_code', 20)->nullable();
			$table->bigInteger('area_code')->nullable();
			$table->float('dma_code')->nullable();
			$table->float('metro_code')->nullable();
			$table->string('continent_code', 2)->nullable();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_sql_queries', function($table)
		{
			$table->bigIncrements('id');

			$table->string('sha1', 40)->index();
			$table->text('statement');
			$table->float('time')->index();
			$table->integer('connection_id')->unsigned();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_sql_query_bindings', function($table)
		{
			$table->bigIncrements('id');

			$table->string('sha1', 40)->index();
			$table->text('serialized');

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_sql_query_bindings_parameters', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('sql_query_bindings_id')->unsigned()->nullable();
			$table->string('name')->nullable()->index();
			$table->text('value')->nullable();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_sql_queries_log', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('log_id')->unsigned()->index();
			$table->bigInteger('sql_query_id')->unsigned()->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_connections', function($table)
		{
			$table->bigIncrements('id');

			$table->string('name')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_events', function($table)
		{
			$table->bigIncrements('id');

			$table->string('name')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_events_log', function($table)
		{
			$table->bigIncrements('id');

			$table->bigInteger('event_id')->unsigned()->index();
			$table->bigInteger('class_id')->unsigned()->index();
			$table->bigInteger('log_id')->unsigned()->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});

		$this->getSchemaBuilder()->create('tracker_system_classes', function($table)
		{
			$table->bigIncrements('id');

			$table->string('name')->index();

			$this->timestamp('created_at')->index();
			$this->timestamp('updated_at')->index();
		});
	}

	public function down()
	{
		$this->execute('dropTables');
	}

	public function dropTables()
	{
		$this->getSchemaBuilder()->drop('tracker_errors');

		$this->getSchemaBuilder()->drop('tracker_sessions');

		$this->getSchemaBuilder()->drop('tracker_referers');

		$this->getSchemaBuilder()->drop('tracker_domains');

		$this->getSchemaBuilder()->drop('tracker_routes');

		$this->getSchemaBuilder()->drop('tracker_route_paths');

		$this->getSchemaBuilder()->drop('tracker_route_path_parameters');

		$this->getSchemaBuilder()->drop('tracker_devices');

		$this->getSchemaBuilder()->drop('tracker_cookies');

		$this->getSchemaBuilder()->drop('tracker_agents');

		$this->getSchemaBuilder()->drop('tracker_query_arguments');

		$this->getSchemaBuilder()->drop('tracker_queries');

		$this->getSchemaBuilder()->drop('tracker_paths');

		$this->getSchemaBuilder()->drop('tracker_log');

		$this->getSchemaBuilder()->drop('tracker_geoip');

		$this->getSchemaBuilder()->drop('tracker_sql_queries');

		$this->getSchemaBuilder()->drop('tracker_sql_queries_log');

		$this->getSchemaBuilder()->drop('tracker_sql_query_bindings');

		$this->getSchemaBuilder()->drop('tracker_sql_query_bindings_parameters');

		$this->getSchemaBuilder()->drop('tracker_connections');

		$this->getSchemaBuilder()->drop('tracker_events');

		$this->getSchemaBuilder()->drop('tracker_events_log');

		$this->getSchemaBuilder()->drop('tracker_system_classes');
	}

	public function getSchemaBuilder()
	{
		return $this->getConnection()->getSchemaBuilder();
	}

	public function getConnection()
	{
		return $this->manager->connection($this->connection);
	}

	public function execute($method)
	{
		$this->getConnection()->beginTransaction();

		try 
		{
			$this->{$method}();
		} 
		catch (\Exception $e) 
		{
			$this->getConnection()->rollback();

			if ($e instanceof \Illuminate\Database\QueryException)
			{
				throw new $e($e->getMessage(), $e->getBindings(), $e->previous);	
			}
			else
			{
				throw new $e($e->getMessage(), $e->previous);
			}
		}

		$this->getConnection()->commit();
	}

}
