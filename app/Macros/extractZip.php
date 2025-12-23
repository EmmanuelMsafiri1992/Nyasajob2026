<?php
namespace App\Macros;

use Illuminate\Filesystem\Filesystem;

/**
 * Extract Zip file
 *
 * Usage: File::extractZip($path, $extractTo);
 *
 * @param string $path
 * @param string $extractTo
 */
Filesystem::macro('extractZip', function ($path, $extractTo) {
	
	$doesServerCan = (extension_loaded('zip') && class_exists('ZipArchive'));
	if ($doesServerCan) {
		try {
			$zip = new ZipArchive();
			$zip->open($path);
			$zip->extractTo($extractTo);
			$zip->close();
		} catch (\Exception $e) {
			$doesServerCan = false;
		}
	}
	
	return $doesServerCan;
});
