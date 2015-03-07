<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

class Tables extends Base {

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
	public function fire()
	{
		$files = $this->laravel->make('files');

		foreach ($files->files($this->getPackageMigrationsPath()) as $file)
		{
			$files->copy($file, $destination = $this->makeMigrationPath($file));

			$this->info("Migration created: $destination");
		}

		if (isLaravel5())
		{
			$this->call('optimize');
		}
		else
		{
			$this->call('dump-autoload');
		}
	}

	/**
	 * Get the package migrations folder
	 *
	 * @return string
	 */
	protected function getPackageMigrationsPath()
	{
		$ds = DIRECTORY_SEPARATOR;

		return __DIR__."{$ds}..{$ds}..{$ds}..{$ds}migrations";
	}

	/**
	 * Get the system migrations folder
	 *
	 * @return string
	 */
	protected function getBaseMigrationsPath()
	{
		$path = 'database'.DIRECTORY_SEPARATOR.'migrations';

		if (isLaravel5())
		{
			return base_path($path);
		}

		return app_path($path);
	}

	/**
	 * Make a full path migration name
	 *
	 * @param $file
	 * @return string
	 */
	private function makeMigrationPath($file)
	{
		return $this->getBaseMigrationsPath().DIRECTORY_SEPARATOR.basename($file);
	}

}
