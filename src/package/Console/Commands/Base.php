<?php

namespace PragmaRX\Version\Package\Console\Commands;

use Illuminate\Console\Command;

class Base extends Command
{
    /**
     * Display the current app version.
     *
     * @param string $format
     */
    public function displayAppVersion($format = 'full')
    {
        $this->info(config('app.name').' '.app('pragmarx.version')->format($format));
    }
}
