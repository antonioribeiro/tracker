<?php

use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracker_sessions', function($table) {
			$table->increments('id');
			
			$table->string('session_uuid');
			$table->string('user_id');
			$table->string('device_id');
			$table->string('client_ip');

			$table->timestamps();
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tracker_sessions');
	}

}