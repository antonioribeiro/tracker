<?php

use PragmaRX\Tracker\Support\Migration;

class CreateTrackerSystemClassesTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_system_classes';

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

                $table->string('name')->index();

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
