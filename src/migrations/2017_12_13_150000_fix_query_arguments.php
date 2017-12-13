<?php

use PragmaRX\Tracker\Support\Migration;
use PragmaRX\Tracker\Vendor\Laravel\Models\Agent;
use PragmaRX\Tracker\Vendor\Laravel\Models\QueryArgument;

class FixQueryArguments extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_query_arguments';

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
                    $table->string('value')->nullable()->index()->change();
                }
            );

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        try {
            $this->builder->table(
                $this->table,
                function ($table) {
                    $table->string('value')->index()->change();
                }
            );

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
