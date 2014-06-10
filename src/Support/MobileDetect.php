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

use Jenssegers\Agent\Agent;

class MobileDetect extends Agent {

	/**
	 * Detect kind, model and mobility.
	 *
	 * @return array
	 */
	public function detectDevice()
	{
		return [
					'kind' => $this->getDeviceKind(),
					'model' => $this->getDeviceName(),
					'is_mobile' => $this->isMobile(),
					'is_robot' => $this->isRobot(),
				];

	}

	/**
	 * Get the kind of device.
	 *
	 * @internal param $mobile
	 * @return string
	 */
	public function getDeviceKind()
	{
		$kind = 'unavailable';

		if ($this->isTablet())
		{
			$kind = ' Tablet';
		}

		elseif ($this->isPhone())
		{
			$kind = 'Phone';
		}

		elseif ($this->isComputer())
		{
			$kind = 'Computer';
		}

		return $kind;
	}

	/**
	 * Get the device name.
	 *
	 * @return int|string
	 */
	public function getDeviceName()
	{
		$devices = array_merge(
			$this->getPhoneDevices(),
			$this->getTabletDevices()
		);

		$model = 'unavailable';

		foreach ($devices as $name => $regex)
		{
			if ($this->{'is' . $name}())
			{
				$model = $name;

				break;
			}
		}

		return $model;
	}

	/**
	 * Is this a phone?
	 *
	 * @return bool
	 */
	public function isPhone()
	{
		return ! $this->isTablet() && ! $this->isComputer();
	}

	/**
	 * Is this a computer?
	 *
	 * @return bool
	 */
	public function isComputer()
	{
		return ! $this->isMobile();
	}

}
