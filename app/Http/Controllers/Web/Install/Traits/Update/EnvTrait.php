<?php
/**
 * Nyasajob - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Web\Install\Traits\Update;

trait EnvTrait
{
	/**
	 * Update the current version to last version
	 *
	 * @param $last
	 */
	private function setCurrentVersion($last)
	{
		setEnvValue('APP_VERSION', $last);
	}
}
