<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Base extends Command
{
    /**
     * The table helper set.
     *
     * @var \Symfony\Component\Console\Helper\TableHelper
     */
    protected $table;

    /**
     * Display all messages.
     *
     * @param $type
     * @param $messages
     */
    public function displayMessages($type, $messages)
    {
        foreach ($messages as $message) {
            $this->$type($message);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['query', InputArgument::IS_ARRAY, 'The SQL query to be executed'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $baseOptions = [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
        ];

        return array_merge($baseOptions, isset($this->options) ? $this->options : []);
    }

    /**
     * Display results.
     *
     * @param $result
     * @param string $method
     */
    public function display($result, $method = 'info')
    {
        if ($result) {
            if (is_array($result)) {
                $this->displayTable($result);
            } elseif (is_bool($result)) {
                $this->{$method}($result ? 'Statement executed sucessfully.' : 'And error ocurred while executing the statement.');
            } else {
                $this->{$method}($result);
            }
        }
    }

    /**
     * Display results in table format.
     *
     * @param $table
     */
    public function displayTable($table)
    {
        $headers = $this->makeHeaders($table[0]);

        $rows = [];

        foreach ($table as $row) {
            $rows[] = (array) $row;
        }

        $this->table = $this->getHelperSet()->get('table');

        $this->table->setHeaders($headers)->setRows($rows);

        $this->table->render($this->getOutput());
    }

    /**
     * Extract headers from result.
     *
     * @param $items
     *
     * @return array
     */
    private function makeHeaders($items)
    {
        return array_keys((array) $items);
    }
}
