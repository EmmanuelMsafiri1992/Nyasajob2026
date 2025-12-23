<?php
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
