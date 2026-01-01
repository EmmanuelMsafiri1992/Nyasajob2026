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
		// Check if exec() function is available (may be disabled on some servers)
		if (!function_exists('exec') || $this->isExecDisabled()) {
			return PHP_VERSION;
		}

		$phpBinaryPath = $this->getPhpBinaryPath();

		if (empty($phpBinaryPath)) {
			return PHP_VERSION;
		}

		try {
			$output = [];
			exec($phpBinaryPath . ' -v 2>&1', $output);

			if (!empty($output[0])) {
				if (preg_match('/PHP (\d+\.\d+\.\d+)/', $output[0], $matches)) {
					return $matches[1];
				}
			}
		} catch (\Throwable $e) {
			return PHP_VERSION;
		}

		return PHP_VERSION;
	}

	/**
	 * Check if exec() function is disabled
	 */
	private function isExecDisabled(): bool
	{
		$disabledFunctions = ini_get('disable_functions');
		if (!empty($disabledFunctions)) {
			$disabledArray = array_map('trim', explode(',', $disabledFunctions));
			return in_array('exec', $disabledArray);
		}
		return false;
	}

	/**
	 * Get PHP components/extensions status
	 */
	public function getComponents(): array
	{
		$components = [];

		$requiredExtensions = [
			'bcmath'    => ['required' => true, 'name' => 'BCMath PHP Extension'],
			'ctype'     => ['required' => true, 'name' => 'Ctype PHP Extension'],
			'curl'      => ['required' => true, 'name' => 'cURL PHP Extension'],
			'dom'       => ['required' => true, 'name' => 'DOM PHP Extension'],
			'fileinfo'  => ['required' => true, 'name' => 'Fileinfo PHP Extension'],
			'gd'        => ['required' => true, 'name' => 'GD PHP Extension'],
			'iconv'     => ['required' => true, 'name' => 'Iconv PHP Extension'],
			'intl'      => ['required' => true, 'name' => 'Intl PHP Extension'],
			'json'      => ['required' => true, 'name' => 'JSON PHP Extension'],
			'mbstring'  => ['required' => true, 'name' => 'Mbstring PHP Extension'],
			'openssl'   => ['required' => true, 'name' => 'OpenSSL PHP Extension'],
			'pdo'       => ['required' => true, 'name' => 'PDO PHP Extension'],
			'pdo_mysql' => ['required' => true, 'name' => 'PDO MySQL PHP Extension'],
			'tokenizer' => ['required' => true, 'name' => 'Tokenizer PHP Extension'],
			'xml'       => ['required' => true, 'name' => 'XML PHP Extension'],
			'zip'       => ['required' => true, 'name' => 'Zip PHP Extension'],
			'exif'      => ['required' => false, 'name' => 'Exif PHP Extension'],
			'imagick'   => ['required' => false, 'name' => 'Imagick PHP Extension'],
			'soap'      => ['required' => false, 'name' => 'SOAP PHP Extension'],
			'sockets'   => ['required' => false, 'name' => 'Sockets PHP Extension'],
		];

		foreach ($requiredExtensions as $ext => $info) {
			$isLoaded = extension_loaded($ext);
			$components[] = [
				'type'     => 'component',
				'name'     => $info['name'],
				'required' => $info['required'],
				'isOk'     => $isLoaded,
				'warning'  => "The <code>{$ext}</code> PHP extension is not installed.",
				'success'  => "The <code>{$ext}</code> PHP extension is installed.",
			];
		}

		return $components;
	}

	/**
	 * Get directory permissions status
	 */
	public function getPermissions(): array
	{
		$permissions = [];

		$directories = [
			storage_path()              => '775',
			storage_path('app')         => '775',
			storage_path('app/public')  => '775',
			storage_path('framework')   => '775',
			storage_path('logs')        => '775',
			base_path('bootstrap/cache') => '775',
		];

		foreach ($directories as $dir => $requiredPermission) {
			$isWritable = is_dir($dir) && is_writable($dir);
			$relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $dir);
			$permissions[] = [
				'type'     => 'permission',
				'name'     => $relativePath,
				'required' => $requiredPermission,
				'isOk'     => $isWritable,
				'warning'  => "The <code>{$relativePath}</code> directory must be writable.",
				'success'  => "The <code>{$relativePath}</code> directory is writable.",
			];
		}

		return $permissions;
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
