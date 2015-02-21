<?php

/**
 * Part of the Tracker package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Tracker
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Support\Config;

class SqlQuery extends Repository {

	private $queries = array();

	/**
	 * @var SqlQueryLog
	 */
	private $sqlQueryLogRepository;

	/**
	 * @var SqlQueryBinding
	 */
	private $sqlQueryBindingRepository;

	/**
	 * @var SqlQueryBindingParameter
	 */
	private $sqlQueryBindingParameterRepository;

	/**
	 * @var Connection
	 */
	private $connectionRepository;

	/**
	 * @var Log
	 */
	private $logRepository;

	/**
	 * @var \PragmaRX\Support\Config
	 */
	private $config;

	public function __construct($model,
	                            SqlQueryLog $sqlQueryLogRepository,
	                            SqlQueryBinding $sqlQueryBindingRepository,
	                            SqlQueryBindingParameter $sqlQueryBindingParameterRepository,
	                            Connection $connectionRepository,
								Log $logRepository,
								Config $config
	)
	{
		parent::__construct($model);

		$this->sqlQueryLogRepository = $sqlQueryLogRepository;

		$this->sqlQueryBindingRepository = $sqlQueryBindingRepository;

		$this->sqlQueryBindingParameterRepository = $sqlQueryBindingParameterRepository;

		$this->connectionRepository = $connectionRepository;

		$this->logRepository = $logRepository;

		$this->config = $config;
	}

	public function fire()
	{
		if ( ! $this->logRepository->getCurrentLogId())
		{
			return;
		}

		foreach ($this->queries as $query)
		{
			$this->logQuery($query);
		}

		$this->clear();
	}

	private function sqlQueryIsLoggable($sqlQuery)
	{
		return strpos($sqlQuery, '"tracker_') === false;
	}

	private function serializeBindings($bindings)
	{
		return serialize($bindings);
	}

	public function push($query)
	{
		$this->queries[] = $query;

		$this->fire();
	}

	private function logQuery($query)
	{
		$sqlQuery = $query['query'];

		$bindings = $query['bindings'];

		$time = $query['time'];

		$name = $query['name'];

		if ( ! $this->sqlQueryIsLoggable($sqlQuery))
		{
			return;
		}

		$connectionId = $this->connectionRepository->findOrCreate(
			array('name' => $name),
			array('name')
		);

		$sqlQueryId = $this->findOrCreate(
			array(
				'sha1'          => sha1($sqlQuery),
				'statement'     => $sqlQuery,
				'time'          => $time,
				'connection_id' => $connectionId,
			),
			array('sha1')
		);

		if ($bindings && $this->canLogBindings())
		{
			$bindingsSerialized = $this->serializeBindings($bindings);

			$sqlQuery_bindings_id = $this->sqlQueryBindingRepository->findOrCreate(
				array('sha1' => sha1($bindingsSerialized), 'serialized' => $bindingsSerialized),
				array('sha1'),
				$created
			);

			if ($created)
			{
				foreach ($bindings as $parameter => $value)
				{
					$this->sqlQueryBindingParameterRepository->create(
						array(
							'sql_query_bindings_id' => $sqlQuery_bindings_id,

							// unfortunately laravel uses question marks,
							// but hopefully someday this will change
							'name'                  => '?',

							'value'                 => $value,
						)
					);
				}
			}
		}

		$this->sqlQueryLogRepository->create(
			array(
				'log_id'       => $this->logRepository->getCurrentLogId(),
				'sql_query_id' => $sqlQueryId,
			)
		);
	}

	private function canLogBindings()
	{
		return $this->config->get('log_sql_queries_bindings');
	}

	/**
	 * @return array
	 */
	private function clear()
	{
		return $this->queries = array();
	}

}

//$data = compact('bindings', 'time', 'name');
//
//// format binding data for sql insertion
//foreach ($bindings as $i => $binding)
//{
//	if ($binding instanceof \DateTime)
//	{
//		$bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
//	}
//	else if (is_string($binding))
//	{
//		$bindings[$i] = "'$binding'";
//	}
//}
//
//$sqlQuery = str_replace(array('%', '?'), array('%%', '%s'), $sqlQuery);
//$sqlQuery = vsprintf($sqlQuery, $bindings);

