<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropUnique('tracker_agents_name_unique');
            });

            Schema::table($this->table, function (Blueprint $table) {
                $table->mediumText('name')->change();
            });

            Schema::table($this->table, function (Blueprint $table) {
                $table->unique('id', 'tracker_agents_name_unique'); // this is a dummy index
            });
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
            Schema::table($this->table, function (Blueprint $table) {
                $table->string('name', 255)->change();
                $table->unique('name');
            });
        } catch (\Exception $e) {
        }
    }
}
