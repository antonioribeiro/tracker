<?php

use Illuminate\Database\Migrations\Migration;

class CreateTrackerAccessesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracker_accesses', function($table) {
			$table->increments('id');
			
			$table->string('session_id');
			$table->string('path_info');

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
		Schema::drop('tracker_accesses');
	}

}