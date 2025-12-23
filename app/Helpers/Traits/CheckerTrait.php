<?php

namespace App\Helpers\Traits;

trait CheckerTrait
{
	use PhpTrait;

	/**
	 * Get PHP binary version
	 */
	public function getPhpBinaryVersion(): ?string
	{
		$phpBinaryPath = $this->getPhpBinaryPath();

		if (empty($phpBinaryPath)) {
			return null;
		}

		try {
			$output = [];
			exec($phpBinaryPath . ' -v 2>&1', $output);

			if (!empty($output[0])) {
				if (preg_match('/PHP (\d+\.\d+\.\d+)/', $output[0], $matches)) {
					return $matches[1];
				}
			}
		} catch (\Exception $e) {
			return null;
		}

		return PHP_VERSION;
	}

	/**
	 * Check system requirements and return any error messages
	 */
	public function getRequirementsErrors(): ?string
	{
		$errors = [];

		// Check PHP version
		$requiredPhpVersion = $this->getComposerRequiredPhpVersion();
		if ($requiredPhpVersion) {
			// Handle multiple version constraints (e.g., "^8.2|^8.3|^8.4")
			// Take the first version constraint and extract the version number
			$versionParts = explode('|', $requiredPhpVersion);
			$firstVersion = trim($versionParts[0]);
			$cleanVersion = preg_replace('/[^0-9.]/', '', $firstVersion);
			if (version_compare(PHP_VERSION, $cleanVersion, '<')) {
				$errors[] = "PHP version {$cleanVersion} or higher is required. Current: " . PHP_VERSION;
			}
		}

		// Check required extensions
		$requiredExtensions = [
			'bcmath',
			'ctype',
			'curl',
			'dom',
			'fileinfo',
			'gd',
			'iconv',
			'intl',
			'json',
			'mbstring',
			'openssl',
			'pdo',
			'pdo_mysql',
			'tokenizer',
			'xml',
			'zip',
		];

		foreach ($requiredExtensions as $ext) {
			if (!extension_loaded($ext)) {
				$errors[] = "PHP extension '{$ext}' is required but not loaded.";
			}
		}

		// Check writable directories
		$writableDirs = [
			storage_path(),
			storage_path('app'),
			storage_path('framework'),
			storage_path('logs'),
			base_path('bootstrap/cache'),
		];

		foreach ($writableDirs as $dir) {
			if (is_dir($dir) && !is_writable($dir)) {
				$errors[] = "Directory '{$dir}' must be writable.";
			}
		}

		return !empty($errors) ? implode('<br>', $errors) : null;
	}
}
