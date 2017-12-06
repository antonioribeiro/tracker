<?php

namespace PragmaRX\Version\Package\Console\Commands;

class Version extends Show
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version {--format= : Use a different format (default: full)}';
}
