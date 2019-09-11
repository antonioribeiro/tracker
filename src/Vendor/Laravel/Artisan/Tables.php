<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

class Tables extends Base
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $name = 'tracker:tables';

    /**
     * Command description.
     *
     * @var string
     */
    protected $description = 'Create the migrations for Tracker database tables and columns';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {
        $files = $this->laravel->make('files');

        foreach ($files->files($this->getPackageMigrationsPath()) as $file) {
            if (!file_exists($destination = $this->makeMigrationPath($file))) {
                $files->copy($file, $destination);

                $this->info("Migration created: $destination");
            }
        }
    }

    /**
     * Get the package migrations folder.
     *
     * @return string
     */
    protected function getPackageMigrationsPath()
    {
        $ds = DIRECTORY_SEPARATOR;

        return __DIR__."{$ds}..{$ds}..{$ds}..{$ds}migrations";
    }

    /**
     * Get the system migrations folder.
     *
     * @return string
     */
    protected function getBaseMigrationsPath()
    {
        $path = 'database'.DIRECTORY_SEPARATOR.'migrations';

        return base_path($path);
    }

    /**
     * Make a full path migration name.
     *
     * @param $file
     *
     * @return string
     */
    private function makeMigrationPath($file)
    {
        return $this->getBaseMigrationsPath().DIRECTORY_SEPARATOR.basename($file);
    }
}
