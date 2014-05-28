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

use Illuminate\Database\Schema\Builder as Schema;

class Migrator
{

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function up()
    {
        $this->schema->create('tracker_log', function($table)
        {
            $table->increments('id');

            $table->integer('session_id')->unsigned();
            $table->integer('path_id')->unsigned();
	        $table->integer('query_id')->unsigned()->nullable();
	        $table->string('method',10);
	        $table->integer('route_path_id')->nullable()->unsigned();
	        $table->boolean('is_ajax');
	        $table->boolean('is_secure');
	        $table->boolean('is_json');
	        $table->boolean('wants_json');
	        $table->integer('error_id')->nullable()->unsigned();

            $table->timestamps();
        });

	    $this->schema->create('tracker_paths', function($table)
	    {
		    $table->increments('id');

		    $table->string('path')->index();

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_queries', function($table)
	    {
		    $table->increments('id');

		    $table->string('query')->index();

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_query_arguments', function($table)
	    {
		    $table->increments('id');

		    $table->integer('query_id')->unsigned()->index();
		    $table->string('argument');
		    $table->string('value');

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_routes', function($table)
	    {
		    $table->increments('id');

		    $table->string('name')->index();
		    $table->string('action')->index();

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_route_paths', function($table)
	    {
		    $table->increments('id');

		    $table->string('route_id')->index();
		    $table->string('path');

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_route_path_parameters', function($table)
	    {
		    $table->increments('id');

		    $table->integer('route_path_id')->unsigned()->index();
		    $table->string('parameter');
		    $table->string('value');

		    $table->timestamps();
	    });

        $this->schema->create('tracker_agents', function($table)
        {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('browser');
            $table->string('browser_version');

            $table->timestamps();
        });

        $this->schema->create('tracker_cookies', function($table)
        {
            $table->increments('id');

            $table->string('uuid')->unique();

            $table->timestamps();
        });

        $this->schema->create('tracker_devices', function($table)
        {
            $table->increments('id');

            $table->string('kind');
            $table->string('model');
            $table->string('platform');
            $table->string('platform_version');
            $table->boolean('is_mobile');
            $table->integer('agent_id')->unsigned();

            $table->unique(['kind', 'model', 'platform', 'platform_version']);

            $table->timestamps();
        });

	    $this->schema->create('tracker_referers', function($table)
	    {
		    $table->increments('id');

		    $table->integer('domain_id')->unsigned()->index();
		    $table->string('referer')->index();
		    $table->string('host');

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_domains', function($table)
	    {
		    $table->increments('id');

		    $table->string('domain')->index();

		    $table->timestamps();
	    });

	    $this->schema->create('tracker_sessions', function($table)
	    {
            $table->increments('id');

            $table->string('uuid')->unique();
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('device_id')->unsigned();
            $table->string('client_ip');
	        $table->integer('referer_id')->unsigned()->nullable();
            $table->integer('cookie_id')->nullable()->unsigned();
            $table->timestamp('last_activity');

            $table->timestamps();
        });

	    $this->schema->create('tracker_errors', function($table)
	    {
		    $table->increments('id');

		    $table->string('code');
		    $table->string('message');

		    $table->timestamps();
	    });
    }

    public function down()
    {
	    $this->schema->drop('tracker_errors');

        $this->schema->drop('tracker_sessions');

	    $this->schema->drop('tracker_referers');

	    $this->schema->drop('tracker_domains');

	    $this->schema->drop('tracker_routes');

	    $this->schema->drop('tracker_route_paths');

	    $this->schema->drop('tracker_route_path_parameters');

        $this->schema->drop('tracker_devices');

        $this->schema->drop('tracker_cookies');

        $this->schema->drop('tracker_agents');

	    $this->schema->drop('tracker_query_arguments');

	    $this->schema->drop('tracker_queries');

	    $this->schema->drop('tracker_paths');

        $this->schema->drop('tracker_log');
    }

}