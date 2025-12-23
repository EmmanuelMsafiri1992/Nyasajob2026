<?php
namespace App\Http\Controllers\Web\Traits;

trait EnvFileTrait
{
	/**
	 * Check & Add the missing entries in the /.env file
	 */
	public function checkDotEnvEntries()
	{
		if (!appInstallFilesExist()) {
			return;
		}

		$envPath = base_path('.env');
		if (!file_exists($envPath)) {
			return;
		}

		$envContent = file_get_contents($envPath);
		$isChanged = false;

		// Check the App Config Locale
		if (!$this->envKeyExists($envContent, 'APP_LOCALE')) {
			$envContent .= "\nAPP_LOCALE=" . config('appLang.abbr');
			$isChanged = true;
		}

		// MySQL Dump Binary Path
		if (!$this->envKeyExists($envContent, 'DB_DUMP_BINARY_PATH')) {
			if ($this->envKeyExists($envContent, 'DB_DUMP_COMMAND_PATH')) {
				$value = env('DB_DUMP_COMMAND_PATH', '');
				$envContent .= "\nDB_DUMP_BINARY_PATH=" . $value;
				$envContent = preg_replace('/^DB_DUMP_COMMAND_PATH=.*$/m', '', $envContent);
			} else {
				$envContent .= "\nDB_DUMP_BINARY_PATH=";
			}
			$isChanged = true;
		}

		// API Options
		if (!$this->envKeyExists($envContent, 'APP_API_TOKEN')) {
			$envContent .= "\nAPP_API_TOKEN=" . base64_encode(createRandomString(32));
			$envContent .= "\nAPP_HTTP_CLIENT=none";
			$isChanged = true;
		}

		if ($isChanged) {
			file_put_contents($envPath, $envContent);
		}
	}

	/**
	 * Check if a key exists in the env content
	 *
	 * @param string $content
	 * @param string $key
	 * @return bool
	 */
	private function envKeyExists($content, $key)
	{
		return preg_match('/^' . preg_quote($key, '/') . '=/m', $content) === 1;
	}
}
