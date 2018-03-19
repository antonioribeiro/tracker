<?php

use PragmaRX\Tracker\Support\Migration;

class CreateTrackerDevicesTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_devices';

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

                $table->string('kind', 16)->index();
                $table->string('model', 64)->index();
                $table->string('platform', 64)->index();
                $table->string('platform_version', 16)->index();
                $table->boolean('is_mobile');

                $table->unique(['kind', 'model', 'platform', 'platform_version']);

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
