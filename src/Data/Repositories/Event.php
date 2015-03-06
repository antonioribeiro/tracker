<?php

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Support\Config;
use PragmaRX\Tracker\Eventing\EventStorage;

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
	 * @var \PragmaRX\Support\Config
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

		foreach ($this->eventStorage->popAll() as $event)
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

		// Illuminate Query may cause infinite recursion
		$forbidden[] = 'illuminate.query';

		return
			$event['event'] != $this->getObject($event['object'])

			&&

			! in_array_wildcard($event['event'], $forbidden)

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

	public function getAll($minutes, $results)
	{
		return $this->getModel()->allInThePeriod($minutes, $results);
	}
}
