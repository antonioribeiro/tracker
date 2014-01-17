<?php

use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration {

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
			$table->boolean('mobile');

			$table->unique(['kind', 'model']);

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