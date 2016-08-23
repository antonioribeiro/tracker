<?php

namespace PragmaRX\Tracker\Support;

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerDetector
{
    /**
     * Crawler detector.
     *
     * @var \Jaybizzle\CrawlerDetect\CrawlerDetect
     */
    private $detector;

    /**
     * Instantiate detector.
     *
     * @param array $headers
     * @param $agent
     */
    public function __construct(array $headers, $agent)
    {
        $this->detector = new CrawlerDetect($headers, $agent);
    }

    /**
     * Check if current request is from a bot.
     *
     * @return bool
     */
    public function isRobot()
    {
        return $this->detector->isCrawler();
    }
}
