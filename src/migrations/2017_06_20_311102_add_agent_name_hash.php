<?php

use PragmaRX\Tracker\Support\Migration;
use PragmaRX\Tracker\Vendor\Laravel\Models\Agent;

class AddAgentNameHash extends Migration
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

                    $table->string('name_hash', 65)->nullable();
                }
            );

            Agent::all()->each(function ($agent) {
                $agent->name_hash = hash('sha256', $agent->name);

                $agent->save();
            });

            $this->builder->table(
                $this->table,
                function ($table) {
                    $table->unique('name_hash');
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
                    $table->dropUnique('tracker_agents_name_hash_unique');

                    $table->dropColumn('name_hash');

                    $table->mediumText('name')->unique()->change();
                }
            );
        } catch (\Exception $e) {
        }
    }
}
