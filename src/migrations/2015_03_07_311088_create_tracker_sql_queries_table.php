<?php

use PragmaRX\Tracker\Support\Migration;

class CreateTrackerSqlQueriesTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_sql_queries';

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

                $table->string('sha1', 40)->index();
                $table->text('statement');
                $table->double('time')->index();
                $table->integer('connection_id')->unsigned();

                $table->timestamp('created_at')->index();
                $table->timestamp('updated_at')->index();
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
