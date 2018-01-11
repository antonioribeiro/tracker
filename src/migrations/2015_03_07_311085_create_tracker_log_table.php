<?php

use PragmaRX\Tracker\Support\Migration;

class CreateTrackerLogTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_log';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        $this->builder->create(
            $this->table,
            function ($table) {
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

                $table->timestamps();
                $table->index('created_at');
                $table->index('updated_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        $this->drop($this->table);
    }
}
