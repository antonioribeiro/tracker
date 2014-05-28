<?php namespace PragmaRX\Tracker\Vendor\Laravel\Artisan;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UAParser\Util\Converter;
use UAParser\Util\Fetcher;

class UpdateParser extends Base {

    /**
     * Command name.
     *
     * @var string
     */
    protected $name = 'tracker:updateparser';

    /**
     * Command description.
     *
     * @var string
     */
    protected $description = 'Update uaparser regexes';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {
        $fetcher = new Fetcher();

        $converter = new Converter(base_path().'/vendor/tobie/ua-parser/php/resources/');

        $converter->convertString($fetcher->fetch(), false);
    }

}