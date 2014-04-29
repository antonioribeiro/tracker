<?php

use Illuminate\Database\Migrations\Migration;

class CreateTrackerAgentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracker_agents', function($table) {
			$table->increments('id');
			
			$table->string('name')->unique();
			$table->string('browser');
			$table->string('browser_version');

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
		Schema::drop('tracker_agents');
	}

}