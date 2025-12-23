<?php
namespace App\Helpers\Files\Storage;

use Illuminate\Support\Facades\Storage;

class StorageDisk
{
	/**
	 * Get the default disk name
	 *
	 * @return \Illuminate\Config\Repository|mixed
	 */
	public static function getDiskName()
	{
		$defaultDisk = config('filesystems.default', 'public');
		
		// $defaultDisk = config('filesystems.cloud'); // Only for tests purpose!
		
		return $defaultDisk;
	}
	
	/**
	 * Get the default disk resources
	 *
	 * @param string|null $name
	 * @return \Illuminate\Contracts\Filesystem\Filesystem
	 */
	public static function getDisk(string $name = null): \Illuminate\Contracts\Filesystem\Filesystem
	{
		$defaultDisk = !is_null($name) ? $name : self::getDiskName();
		
		return Storage::disk($defaultDisk);
	}
}
