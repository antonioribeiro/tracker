<?php

namespace PragmaRX\Tracker\Support;

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerDetector {

	/**
	 * @var \Jaybizzle\CrawlerDetect\CrawlerDetect
	 */
	private $detector;

	/**
	 * @param \Jaybizzle\CrawlerDetect\CrawlerDetect $detector
	 */
	public function __construct(CrawlerDetect $detector)
	{
		$this->detector = $detector;
	}

	public function isRobot()
	{
		return $this->detector->isCrawler();
	}
	
}
