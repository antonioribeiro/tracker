<?php

use PragmaRX\Tracker\Support\Migration;

class CreateTrackerGeoipTable extends Migration
{
    /**
     * Table related to this migration.
     *
     * @var string
     */
    private $table = 'tracker_geoip';

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

                $table->double('latitude')->nullable()->index();
                $table->double('longitude')->nullable()->index();

                $table->string('country_code', 2)->nullable()->index();
                $table->string('country_code3', 3)->nullable()->index();
                $table->string('country_name')->nullable()->index();
                $table->string('region', 2)->nullable();
                $table->string('city', 50)->nullable()->index();
                $table->string('postal_code', 20)->nullable();
                $table->bigInteger('area_code')->nullable();
                $table->double('dma_code')->nullable();
                $table->double('metro_code')->nullable();
                $table->string('continent_code', 2)->nullable();

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
