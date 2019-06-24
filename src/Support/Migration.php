<?php

namespace PragmaRX\Tracker\Support;

use PragmaRX\Support\Migration as PragmaRxMigration;

abstract class Migration extends PragmaRxMigration
{
    protected function checkConnection()
    {
        $this->manager = app()->make('db');

        $this->connection = $this->manager->connection(config('tracker.connection'));

        parent::checkConnection();
    }
}
