<?php

use PragmaRX\Tracker\Support\Migration;

class FixAgentName extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_agents';

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
                    $table->dropUnique('tracker_agents_name_unique');
                }
            );

            $this->builder->table(
                $this->table,
                function ($table) {
                    $table->mediumText('name')->change();
                }
            );

            $this->builder->table(
                $this->table,
                function ($table) {
                    $table->unique('id', 'tracker_agents_name_unique'); // this is a dummy index
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
                    $table->string('name', 255)->change();
                    $table->unique('name');
                }
            );
        } catch (\Exception $e) {
        }
    }
}
