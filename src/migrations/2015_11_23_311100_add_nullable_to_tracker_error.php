<?php

use PragmaRX\Tracker\Support\Migration;

class AddNullableToTrackerError extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_errors';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        try {
            $this->builder->table(
                $this->table,
                function ($table) {
                    $table->string('code')->nullable()->change();
                }
            );
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
    }
}
