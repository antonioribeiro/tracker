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
 * @version    1.0.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Tracker\Eventing\EventStorage;
use PragmaRX\Tracker\Support\Config;

class Event extends Repository {

	/**
	 * @var EventLog
	 */
	private $eventLogRepository;

	/**
	 * @var SystemClass
	 */
	private $systemClassRepository;

	/**
	 * @var Log
	 */
	private $logRepository;

	/**
	 * @var \PragmaRX\Tracker\Support\Config
	 */
	private $config;

	/**
	 * @var \PragmaRX\Tracker\Eventing\EventStorage
	 */
	private $eventStorage;

	public function __construct(
		$model,
		EventStorage $eventStorage,
	    EventLog $eventLogRepository,
	    SystemClass $systemClassRepository,
	    Log $logRepository,
		Config $config
	)
	{
		parent::__construct($model);

		$this->eventStorage = $eventStorage;

		$this->eventLogRepository = $eventLogRepository;

		$this->systemClassRepository = $systemClassRepository;

		$this->logRepository = $logRepository;

		$this->config = $config;
	}
	
	public function logEvents()
	{
		if ( ! $this->logRepository->getCurrentLogId())
		{
			return;
		}

		foreach($this->eventStorage->popAll() as $event)
		{
			if ($this->isLoggableEvent($event))
			{
				$this->logEvent($event);
			}
		}
	}

	private function isLoggableEvent($event)
	{
		$forbidden = $this->config->get('do_not_log_events');

		// Illuminate Query may cause infinite recursivity
		$forbidden[] = 'illuminate.query';

		return
			$event['event'] != $this->getObject($event['object'])

			&&

			! $this->inArrayWildcard($event['event'], $forbidden)

			&&

			! $this->config->get('log_only_events')
				|| in_array($event['event'], $this->config->get('log_only_events'))

			;
	}

	private function logEvent($event)
	{
		$evenId = $event['event']
					? $this->findOrCreate(
							array('name' => $event['event']),
							array('name')
						)
					: null;

		$classId = $this->getObject($event['object'])
					? $this->systemClassRepository->findOrCreate(
							array('name' => $this->getObject($event['object'])),
							array('name')
						)
					: null;

		if ($evenId)
		{
			$this->eventLogRepository->create(
				array(
					'log_id'   => $this->logRepository->getCurrentLogId(),
					'event_id' => $evenId,
					'class_id' => $classId,
				)
			);
		}
	}

	private function getObject($object)
	{
		if (is_object($object))
		{
			$object = get_class($object);
		}
		else
		if(is_array($object))
		{
			$object = serialize($object);
		}

		return $object;
	}

	private function inArrayWildcard($event, $forbidden)
	{
		foreach($forbidden as $pattern)
		{
			if (str_is($pattern, $event))
			{
				return true;
			}
		}
	}

	public function getAll($minutes)
	{
		return $this->getModel()->allInThePeriod($minutes);
	}
}
