<?php

use Illuminate\Database\Migrations\Migration;

class CreateCookiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracker_cookies', function($table) {
			$table->increments('id');
			
			$table->string('uuid')->unique();

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
		Schema::drop('tracker_cookies');
	}

}