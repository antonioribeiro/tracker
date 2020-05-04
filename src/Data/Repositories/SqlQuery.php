<?php

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Support\Config;

class SqlQuery extends Repository
{
    private $queries = [];

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

    public function __construct(
        $model,
        SqlQueryLog $sqlQueryLogRepository,
        SqlQueryBinding $sqlQueryBindingRepository,
        SqlQueryBindingParameter $sqlQueryBindingParameterRepository,
        Connection $connectionRepository,
        Log $logRepository,
        Config $config
    ) {
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
        if (!$this->logRepository->getCurrentLogId()) {
            return;
        }

        foreach ($this->queries as $query) {
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
        $sqlQuery = htmlentities($query['query']);

        $bindings = $query['bindings'];

        $time = $query['time'];

        $name = $query['name'];

        if (!$this->sqlQueryIsLoggable($sqlQuery)) {
            return;
        }

        $connectionId = $this->connectionRepository->findOrCreate(
            ['name' => $name],
            ['name']
        );

        $sqlQueryId = $this->findOrCreate(
            [
                'sha1'          => sha1($sqlQuery),
                'statement'     => $sqlQuery,
                'time'          => $time,
                'connection_id' => $connectionId,
            ],
            ['sha1']
        );

        if ($bindings && $this->canLogBindings()) {
            $bindingsSerialized = $this->serializeBindings($bindings);

            $sqlQuery_bindings_id = $this->sqlQueryBindingRepository->findOrCreate(
                ['sha1' => sha1($bindingsSerialized), 'serialized' => $bindingsSerialized],
                ['sha1'],
                $created
            );

            if ($created) {
                foreach ($bindings as $parameter => $value) {
                    $this->sqlQueryBindingParameterRepository->create(
                        [
                            'sql_query_bindings_id' => $sqlQuery_bindings_id,

                            // unfortunately laravel uses question marks,
                            // but hopefully someday this will change
                            'name'                  => '?',

                            'value'                 => $value,
                        ]
                    );
                }
            }
        }

        $this->sqlQueryLogRepository->create(
            [
                'log_id'       => $this->logRepository->getCurrentLogId(),
                'sql_query_id' => $sqlQueryId,
            ]
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
        return $this->queries = [];
    }
}
