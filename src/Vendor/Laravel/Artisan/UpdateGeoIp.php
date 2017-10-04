<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

class UpdateGeoIp extends Base
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tracker:updategeoip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the GeoIP database.';

    /**
     * Update the geo ip database.
     *
     * @return void
     */
    public function fire()
    {
        $tracker = app('tracker');

        $type = $tracker->updateGeoIp()
            ? 'info'
            : 'error';

        $this->displayMessages($type, $tracker->getMessages());
    }

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->fire();
    }
}
