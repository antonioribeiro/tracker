<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerSqlQueryBindingsParametersTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_sql_query_bindings_parameters';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('sql_query_bindings_id')->unsigned()->nullable();
            $table->string('name')->nullable()->index();
            $table->text('value')->nullable();

            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::dropIfExists($this->table);
    }
}
