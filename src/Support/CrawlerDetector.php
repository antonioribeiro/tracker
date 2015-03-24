<?php

namespace PragmaRX\Tracker\Support;

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerDetector {

	/**
	 * @var \Jaybizzle\CrawlerDetect\CrawlerDetect
	 */
	private $detector;

	/**
	 * @param array $headers
	 * @param $agent
	 */
	public function __construct(array $headers, $agent)
	{
		$this->detector = new CrawlerDetect($headers, $agent);
	}

	public function isRobot()
	{
		return $this->detector->isCrawler();
	}
	
}
