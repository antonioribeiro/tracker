<?php namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

class Tables extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'tracker:tables';

	private $tables = [
		'create_tracker_tables',
	];

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
		foreach ($this->tables as $table)
		{
			$fullPath = $this->createBaseMigration($table);

			file_put_contents($fullPath, $this->getMigrationStub($table));

			$this->info("Migration $table created successfully!");
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
	 * Create a base migration file for the reminders.
	 *
	 * @param $name
	 * @return string
	 */
	protected function createBaseMigration($name)
	{
		if (isLaravel5())
		{
			$path = base_path('/database/migrations');
		}
		else
		{
			$path = $this->laravel['path'].'/database/migrations';
		}

		return $this->laravel['migration.creator']->create($name, $path);
	}

	/**
	 * Get the contents of a migration stub.
	 *
	 * @param $name
	 * @return string
	 */
	protected function getMigrationStub($name)
	{
		$stub = file_get_contents(__DIR__."/../../../stubs/$name.stub");

		return $stub;
	}
}
