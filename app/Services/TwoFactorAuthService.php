<?php
/*
 * JobClass - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
	/**
	 * Check if 2FA is enabled globally
	 *
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return config('settings.authentication.two_factor_enabled') == '1';
	}

	/**
	 * Get the 2FA method
	 *
	 * @return string
	 */
	public function getMethod(): string
	{
		return config('settings.authentication.two_factor_method', 'totp');
	}

	/**
	 * Generate a new secret key for TOTP
	 *
	 * @return string
	 */
	public function generateSecretKey(): string
	{
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$secret = '';
		for ($i = 0; $i < 16; $i++) {
			$secret .= $chars[random_int(0, strlen($chars) - 1)];
		}
		return $secret;
	}

	/**
	 * Generate recovery codes
	 *
	 * @param int $count
	 * @return Collection
	 */
	public function generateRecoveryCodes(int $count = 8): Collection
	{
		return Collection::times($count, function () {
			return Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4));
		});
	}

	/**
	 * Enable 2FA for a user
	 *
	 * @param User $user
	 * @return array
	 */
	public function enable(User $user): array
	{
		$secret = $this->generateSecretKey();
		$recoveryCodes = $this->generateRecoveryCodes();

		$user->two_factor_secret = encrypt($secret);
		$user->two_factor_recovery_codes = encrypt($recoveryCodes->toJson());
		$user->save();

		return [
			'secret' => $secret,
			'recovery_codes' => $recoveryCodes->toArray(),
			'qr_code_url' => $this->getQrCodeUrl($user, $secret),
		];
	}

	/**
	 * Confirm 2FA setup
	 *
	 * @param User $user
	 * @param string $code
	 * @return bool
	 */
	public function confirm(User $user, string $code): bool
	{
		if ($this->verify($user, $code)) {
			$user->two_factor_confirmed_at = now();
			$user->save();
			return true;
		}
		return false;
	}

	/**
	 * Disable 2FA for a user
	 *
	 * @param User $user
	 * @return void
	 */
	public function disable(User $user): void
	{
		$user->two_factor_secret = null;
		$user->two_factor_recovery_codes = null;
		$user->two_factor_confirmed_at = null;
		$user->save();
	}

	/**
	 * Verify a TOTP code
	 *
	 * @param User $user
	 * @param string $code
	 * @return bool
	 */
	public function verify(User $user, string $code): bool
	{
		if (empty($user->two_factor_secret)) {
			return false;
		}

		try {
			$secret = decrypt($user->two_factor_secret);
		} catch (\Throwable $e) {
			return false;
		}

		// Check if it's a recovery code first
		if ($this->verifyRecoveryCode($user, $code)) {
			return true;
		}

		// Verify TOTP code
		return $this->verifyTotpCode($secret, $code);
	}

	/**
	 * Verify a TOTP code against the secret
	 *
	 * @param string $secret
	 * @param string $code
	 * @param int $window
	 * @return bool
	 */
	protected function verifyTotpCode(string $secret, string $code, int $window = 1): bool
	{
		$currentTimestamp = floor(time() / 30);

		for ($i = -$window; $i <= $window; $i++) {
			$calculatedCode = $this->generateTotpCode($secret, $currentTimestamp + $i);
			if (hash_equals($calculatedCode, $code)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate a TOTP code
	 *
	 * @param string $secret
	 * @param int|null $timestamp
	 * @return string
	 */
	protected function generateTotpCode(string $secret, ?int $timestamp = null): string
	{
		$timestamp = $timestamp ?? floor(time() / 30);

		// Decode base32 secret
		$secret = $this->base32Decode($secret);

		// Pack timestamp as 8 bytes
		$time = pack('N*', 0, $timestamp);

		// Generate HMAC-SHA1
		$hmac = hash_hmac('sha1', $time, $secret, true);

		// Get offset
		$offset = ord($hmac[19]) & 0x0f;

		// Generate code
		$code = (
			((ord($hmac[$offset]) & 0x7f) << 24) |
			((ord($hmac[$offset + 1]) & 0xff) << 16) |
			((ord($hmac[$offset + 2]) & 0xff) << 8) |
			(ord($hmac[$offset + 3]) & 0xff)
		) % 1000000;

		return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
	}

	/**
	 * Decode a base32 encoded string
	 *
	 * @param string $input
	 * @return string
	 */
	protected function base32Decode(string $input): string
	{
		$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$output = '';
		$v = 0;
		$vbits = 0;

		for ($i = 0; $i < strlen($input); $i++) {
			$v <<= 5;
			$pos = strpos($alphabet, strtoupper($input[$i]));
			if ($pos !== false) {
				$v += $pos;
			}
			$vbits += 5;
			if ($vbits >= 8) {
				$vbits -= 8;
				$output .= chr(($v >> $vbits) & 0xFF);
			}
		}

		return $output;
	}

	/**
	 * Verify a recovery code
	 *
	 * @param User $user
	 * @param string $code
	 * @return bool
	 */
	protected function verifyRecoveryCode(User $user, string $code): bool
	{
		if (empty($user->two_factor_recovery_codes)) {
			return false;
		}

		try {
			$codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
		} catch (\Throwable $e) {
			return false;
		}

		$code = strtoupper(str_replace('-', '', $code));

		foreach ($codes as $key => $recoveryCode) {
			$storedCode = strtoupper(str_replace('-', '', $recoveryCode));
			if (hash_equals($storedCode, $code)) {
				// Remove used recovery code
				unset($codes[$key]);
				$user->two_factor_recovery_codes = encrypt(json_encode(array_values($codes)));
				$user->save();
				return true;
			}
		}

		return false;
	}

	/**
	 * Get QR code URL for authenticator app
	 *
	 * @param User $user
	 * @param string $secret
	 * @return string
	 */
	public function getQrCodeUrl(User $user, string $secret): string
	{
		$appName = config('app.name', 'JobClass');
		$email = $user->email ?? $user->phone ?? 'user';

		$otpAuthUrl = sprintf(
			'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
			urlencode($appName),
			urlencode($email),
			$secret,
			urlencode($appName)
		);

		// Return Google Charts QR code URL
		return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpAuthUrl);
	}

	/**
	 * Check if user has 2FA enabled
	 *
	 * @param User $user
	 * @return bool
	 */
	public function userHas2FA(User $user): bool
	{
		return !empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at);
	}

	/**
	 * Get remaining recovery codes count
	 *
	 * @param User $user
	 * @return int
	 */
	public function getRemainingRecoveryCodesCount(User $user): int
	{
		if (empty($user->two_factor_recovery_codes)) {
			return 0;
		}

		try {
			$codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
			return is_array($codes) ? count($codes) : 0;
		} catch (\Throwable $e) {
			return 0;
		}
	}
}
