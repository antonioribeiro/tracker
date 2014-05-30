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
        $this->getSchemaBuilder()->create('tracker_log', function($table)
        {
            $table->increments('id');

            $table->integer('session_id')->unsigned();
            $table->integer('path_id')->unsigned();
	        $table->integer('query_id')->unsigned()->nullable();
	        $table->string('method',10);
	        $table->integer('route_path_id')->unsigned()->nullable();
	        $table->boolean('is_ajax');
	        $table->boolean('is_secure');
	        $table->boolean('is_json');
	        $table->boolean('wants_json');
	        $table->integer('error_id')->unsigned()->nullable();

            $table->timestamps();
        });

	    $this->getSchemaBuilder()->create('tracker_paths', function($table)
	    {
		    $table->increments('id');

		    $table->string('path')->index();

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_queries', function($table)
	    {
		    $table->increments('id');

		    $table->string('query')->index();

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_query_arguments', function($table)
	    {
		    $table->increments('id');

		    $table->integer('query_id')->unsigned()->index();
		    $table->string('argument');
		    $table->string('value');

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_routes', function($table)
	    {
		    $table->increments('id');

		    $table->string('name')->index();
		    $table->string('action')->index();

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_route_paths', function($table)
	    {
		    $table->increments('id');

		    $table->string('route_id')->index();
		    $table->string('path');

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_route_path_parameters', function($table)
	    {
		    $table->increments('id');

		    $table->integer('route_path_id')->unsigned()->index();
		    $table->string('parameter');
		    $table->string('value');

		    $table->timestamps();
	    });

        $this->getSchemaBuilder()->create('tracker_agents', function($table)
        {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('browser');
            $table->string('browser_version');

            $table->timestamps();
        });

        $this->getSchemaBuilder()->create('tracker_cookies', function($table)
        {
            $table->increments('id');

            $table->string('uuid')->unique();

            $table->timestamps();
        });

        $this->getSchemaBuilder()->create('tracker_devices', function($table)
        {
            $table->increments('id');

            $table->string('kind');
            $table->string('model');
            $table->string('platform');
            $table->string('platform_version');
            $table->boolean('is_mobile');

            $table->unique(['kind', 'model', 'platform', 'platform_version']);

            $table->timestamps();
        });

	    $this->getSchemaBuilder()->create('tracker_referers', function($table)
	    {
		    $table->increments('id');

		    $table->integer('domain_id')->unsigned()->index();
		    $table->string('url')->index();
		    $table->string('host');

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_domains', function($table)
	    {
		    $table->increments('id');

		    $table->string('name')->index();

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_sessions', function($table)
	    {
            $table->increments('id');

            $table->string('uuid')->unique();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('device_id')->unsigned();
            $table->integer('agent_id')->unsigned();
            $table->string('client_ip');
	        $table->integer('referer_id')->unsigned()->nullable();
            $table->integer('cookie_id')->unsigned()->nullable();
		    $table->integer('geoip_id')->unsigned()->nullable();

            $table->timestamps();
        });

	    $this->getSchemaBuilder()->create('tracker_errors', function($table)
	    {
		    $table->increments('id');

		    $table->string('code');
		    $table->string('message');

		    $table->timestamps();
	    });

	    $this->getSchemaBuilder()->create('tracker_geoip', function($table)
	    {
		    $table->increments('id');

		    $table->float('latitude')->nullable()->index();
		    $table->float('longitude')->nullable()->index();

		    $table->string('country_code', 2)->nullable();
		    $table->string('country_code3', 3)->nullable();
		    $table->string('country_name')->nullable();
		    $table->string('region', 2)->nullable();
		    $table->string('city', 50)->nullable();
		    $table->string('postal_code', 20)->nullable();
		    $table->integer('area_code')->nullable();
		    $table->float('dma_code')->nullable();
		    $table->float('metro_code')->nullable();
		    $table->string('continent_code', 2)->nullable();

		    $table->timestamps();
	    });
    }

    public function down()
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
    }

    public function getSchemaBuilder()
    {
        return $this->manager->connection($this->connection)->getSchemaBuilder();
    }

}