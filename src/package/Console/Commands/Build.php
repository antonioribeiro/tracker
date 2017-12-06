<?php

namespace PragmaRX\Version\Package\Console\Commands;

class Build extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:build {--increment-by= : Increment by a different amount than config }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment app build number';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $build = app('pragmarx.version')->incrementBuild($this->option('increment-by'));

        $this->info("New build: {$build}");

        $this->displayAppVersion();
    }
}
