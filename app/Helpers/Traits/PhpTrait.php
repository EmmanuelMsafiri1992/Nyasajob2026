<?php

namespace App\Helpers\Traits;

trait PhpTrait
{
	/**
	 * Get the PHP binary path
	 */
	public function getPhpBinaryPath(): string
	{
		$phpBinaryPath = PHP_BINARY;

		if (empty($phpBinaryPath)) {
			$phpBinaryPath = defined('PHP_BINDIR') ? PHP_BINDIR . '/php' : '/usr/bin/php';
		}

		return $phpBinaryPath;
	}

	/**
	 * Get composer required PHP version
	 */
	public function getComposerRequiredPhpVersion(): ?string
	{
		$composerFile = base_path('composer.json');

		if (!file_exists($composerFile)) {
			return null;
		}

		$composer = json_decode(file_get_contents($composerFile), true);

		return $composer['require']['php'] ?? null;
	}
}
