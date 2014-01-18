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

use Mobile_Detect;

class MobileDetect extends Mobile_Detect {

	public function detectDevice()
	{
		$mobile = $this->isMobile();

		if ( $this->isTablet() )
		{
			$kind = ' Tablet';
		}
		elseif ( $this->isPhone() )
		{
			$kind = 'Phone';
		}
		else
		{	
			$kind = !$mobile ? 'Computer' : 'Phone';
		}
		
		$model = 'unavailable';
		$model = $this->isiPhone() ? 'iPhone' : $model;
		$model = $this->isiPad() ? 'iPad' : $model;
		$model = $this->isBlackBerry() ? 'BlackBerry' : $model;
		$model = $this->isHTC() ? 'HTC' : $model;
		$model = $this->isNexus() ? 'Nexus' : $model;
		$model = $this->isDell() ? 'Dell' : $model;
		$model = $this->isMotorola() ? 'Motorola' : $model;
		$model = $this->isSamsung() ? 'Samsung' : $model;
		$model = $this->isLG() ? 'LG' : $model;
		$model = $this->isSony() ? 'Sony' : $model;
		$model = $this->isAsus() ? 'Asus' : $model;
		$model = $this->isPalm() ? 'Palm' : $model;
		$model = $this->isVertu() ? 'Vertu' : $model;
		$model = $this->isPantech() ? 'Pantech' : $model;
		$model = $this->isFly() ? 'Fly' : $model;
		$model = $this->isSimValley() ? 'SimValley' : $model;
		$model = $this->isGenericPhone() ? 'GenericPhone' : $model;
		$model = $this->isNexusTablet() ? 'Nexus Tablet' : $model;
		$model = $this->isSamsungTablet() ? 'Samsung Tablet' : $model;
		$model = $this->isKindle() ? 'Kindle' : $model;
		$model = $this->isSurfaceTablet() ? 'Surface Tablet' : $model;
		$model = $this->isAsusTablet() ? 'Asus Tablet' : $model;
		$model = $this->isBlackBerryTablet() ? 'BlackBerry Tablet' : $model;
		$model = $this->isHTCtablet() ? 'HTC tablet' : $model;
		$model = $this->isMotorolaTablet() ? 'Motorola Tablet' : $model;
		$model = $this->isNookTablet() ? 'Nook Tablet' : $model;
		$model = $this->isAcerTablet() ? 'Acer Tablet' : $model;
		$model = $this->isToshibaTablet() ? 'Toshiba Tablet' : $model;
		$model = $this->isLGTablet() ? 'LG Tablet' : $model;
		$model = $this->isYarvikTablet() ? 'Yarvik Tablet' : $model;
		$model = $this->isMedionTablet() ? 'Medion Tablet' : $model;
		$model = $this->isArnovaTablet() ? 'Arnova Tablet' : $model;
		$model = $this->isArchosTablet() ? 'Archos Tablet' : $model;
		$model = $this->isAinolTablet() ? 'Ainol Tablet' : $model;
		$model = $this->isSonyTablet() ? 'Sony Tablet' : $model;
		$model = $this->isCubeTablet() ? 'Cube Tablet' : $model;
		$model = $this->isCobyTablet() ? 'Coby Tablet' : $model;
		$model = $this->isSMiTTablet() ? 'SMiT Tablet' : $model;
		$model = $this->isRockChipTablet() ? 'RockChip Tablet' : $model;
		$model = $this->isTelstraTablet() ? 'Telstra Tablet' : $model;
		$model = $this->isFlyTablet() ? 'Fly Tablet' : $model;
		$model = $this->isbqTablet() ? 'bq Tablet' : $model;
		$model = $this->isHuaweiTablet() ? 'Huawei Tablet' : $model;
		$model = $this->isNecTablet() ? 'Nec Tablet' : $model;
		$model = $this->isPantechTablet() ? 'Pantech Tablet' : $model;
		$model = $this->isBronchoTablet() ? 'Broncho Tablet' : $model;
		$model = $this->isVersusTablet() ? 'Versus Tablet' : $model;
		$model = $this->isZyncTablet() ? 'Zync Tablet' : $model;
		$model = $this->isPositivoTablet() ? 'Positivo Tablet' : $model;
		$model = $this->isNabiTablet() ? 'Nabi Tablet' : $model;
		$model = $this->isPlaystationTablet() ? 'Playstation Tablet' : $model;
		$model = $this->isGenericTablet() ? 'Generic Tablet' : $model;

		return [
					'kind' => trim($kind),
					'model' => trim($model),
					'is_mobile' => $mobile,
				];

	}

}