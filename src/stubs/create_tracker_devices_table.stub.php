<?php

use Illuminate\Database\Migrations\Migration;

class CreateTrackerDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracker_devices', function($table) {
			$table->increments('id');
			
			$table->string('kind');
			$table->string('model');
			$table->string('platform');
			$table->string('platform_version');
			$table->boolean('is_mobile');
			$table->string('agent_id');

			$table->unique(['kind', 'model', 'platform', 'platform_version']);

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
		Schema::drop('tracker_devices');
	}

}