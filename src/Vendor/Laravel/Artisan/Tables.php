<?php namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

class Tables extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'tracker:tables';

	private $tables = [
		'create_tracker_accesses_table',
		'create_tracker_sessions_table',
		'create_tracker_agents_table',
		'create_tracker_cookies_table',
		'create_tracker_devices_table',
	];

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Create all migrations for the Tracker database columns';

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function fire()
	{
		parent::fire();

		foreach($this->tables as $table)
		{
			$fullPath = $this->createBaseMigration($table);

			file_put_contents($fullPath, $this->getMigrationStub($table));

			$this->info("Migration $table created successfully!");
		}

		$this->call('dump-autoload');
	}

	/**
	 * Create a base migration file for the reminders.
	 *
	 * @param $name
	 * @return string
	 */
	protected function createBaseMigration($name)
	{
		$path = $this->laravel['path'].'/database/migrations';

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
		$stub = file_get_contents(__DIR__."/../../../stubs/$name.stub.php");

		return $stub;
	}
}
