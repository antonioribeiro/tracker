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
 
namespace PragmaRX\Tracker\Support;

use UAParser\Parser;
use UAParser\Util\Converter;
use UAParser\Util\Fetcher;

class UserAgentParser {

	public $parser;

	public $userAgent;

	public $operatingSystem;

	public $device;

	public $originalUserAgent;

	public function __construct($basePath, $userAgent = null)
	{
		if (! $userAgent && isset($_SERVER['HTTP_USER_AGENT']))
		{
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
		}

		$this->parser = Parser::create()->parse($userAgent);

		$this->userAgent = $this->parser->ua;

		$this->operatingSystem = $this->parser->os;

		$this->device = $this->parser->device;

		$this->basePath = $basePath;

		$this->originalUserAgent = $this->parser->originalUserAgent;
	}

	public function getOperatingSystemVersion()
	{
		return 	$this->operatingSystem->major .
				($this->operatingSystem->minor !== null ? '.'.$this->operatingSystem->minor : '') .
				($this->operatingSystem->patch !== null ? '.'.$this->operatingSystem->patch : '');
	}

	public function getUserAgentVersion()
	{
		return  $this->userAgent->major .
				($this->userAgent->minor !== null ? '.'.$this->userAgent->minor : '') .
				($this->userAgent->patch !== null ? '.'.$this->userAgent->patch : '');
	}

}