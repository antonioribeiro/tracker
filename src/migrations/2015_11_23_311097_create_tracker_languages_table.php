<?php

use PragmaRX\Tracker\Support\Migration;

class CreateTrackerLanguagesTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_languages';

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

                $table->string('preference')->index();
                $table->string('language-range')->index();

                $table->unique(['preference', 'language-range']);

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
