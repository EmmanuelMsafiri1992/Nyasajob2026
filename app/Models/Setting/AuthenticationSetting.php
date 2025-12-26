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

namespace App\Models\Setting;

/*
 * settings.authentication.option
 */

class AuthenticationSetting
{
	public static function getValues($value, $disk)
	{
		if (empty($value)) {
			$value['two_factor_enabled'] = '0';
			$value['two_factor_method'] = 'totp';
			$value['account_lockout_enabled'] = '1';
			$value['max_login_attempts'] = '5';
			$value['lockout_duration'] = '15';
			$value['password_min_length'] = '6';
			$value['password_require_uppercase'] = '0';
			$value['password_require_number'] = '0';
			$value['password_require_special'] = '0';
			$value['session_timeout'] = '120';
			$value['single_session'] = '0';
		} else {
			if (!array_key_exists('two_factor_enabled', $value)) {
				$value['two_factor_enabled'] = '0';
			}
			if (!array_key_exists('two_factor_method', $value)) {
				$value['two_factor_method'] = 'totp';
			}
			if (!array_key_exists('account_lockout_enabled', $value)) {
				$value['account_lockout_enabled'] = '1';
			}
			if (!array_key_exists('max_login_attempts', $value)) {
				$value['max_login_attempts'] = '5';
			}
			if (!array_key_exists('lockout_duration', $value)) {
				$value['lockout_duration'] = '15';
			}
			if (!array_key_exists('password_min_length', $value)) {
				$value['password_min_length'] = '6';
			}
			if (!array_key_exists('password_require_uppercase', $value)) {
				$value['password_require_uppercase'] = '0';
			}
			if (!array_key_exists('password_require_number', $value)) {
				$value['password_require_number'] = '0';
			}
			if (!array_key_exists('password_require_special', $value)) {
				$value['password_require_special'] = '0';
			}
			if (!array_key_exists('session_timeout', $value)) {
				$value['session_timeout'] = '120';
			}
			if (!array_key_exists('single_session', $value)) {
				$value['single_session'] = '0';
			}
		}

		return $value;
	}

	public static function setValues($value, $setting)
	{
		return $value;
	}

	public static function getFields($diskName)
	{
		$fields = [
			[
				'name'  => 'two_factor_sep',
				'type'  => 'custom_html',
				'value' => '<h3>' . trans('admin.two_factor_authentication') . '</h3>',
			],
			[
				'name'              => 'two_factor_enabled',
				'label'             => trans('admin.two_factor_enabled_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.two_factor_enabled_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'two_factor_method',
				'label'             => trans('admin.two_factor_method_label'),
				'type'              => 'select2_from_array',
				'options'           => [
					'totp'  => trans('admin.two_factor_method_totp'),
					'email' => trans('admin.two_factor_method_email'),
					'sms'   => trans('admin.two_factor_method_sms'),
				],
				'hint'              => trans('admin.two_factor_method_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],

			[
				'name'  => 'account_lockout_sep',
				'type'  => 'custom_html',
				'value' => '<h3>' . trans('admin.account_lockout') . '</h3>',
			],
			[
				'name'              => 'account_lockout_enabled',
				'label'             => trans('admin.account_lockout_enabled_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.account_lockout_enabled_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-12',
				],
			],
			[
				'name'              => 'max_login_attempts',
				'label'             => trans('admin.max_login_attempts_label'),
				'type'              => 'number',
				'attributes'        => [
					'min'  => 1,
					'max'  => 20,
					'step' => 1,
				],
				'hint'              => trans('admin.max_login_attempts_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'lockout_duration',
				'label'             => trans('admin.lockout_duration_label'),
				'type'              => 'number',
				'attributes'        => [
					'min'  => 1,
					'max'  => 1440,
					'step' => 1,
				],
				'hint'              => trans('admin.lockout_duration_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],

			[
				'name'  => 'password_policy_sep',
				'type'  => 'custom_html',
				'value' => '<h3>' . trans('admin.password_policy') . '</h3>',
			],
			[
				'name'              => 'password_min_length',
				'label'             => trans('admin.password_min_length_label'),
				'type'              => 'number',
				'attributes'        => [
					'min'  => 4,
					'max'  => 32,
					'step' => 1,
				],
				'hint'              => trans('admin.password_min_length_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'password_require_uppercase',
				'label'             => trans('admin.password_require_uppercase_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.password_require_uppercase_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'password_require_number',
				'label'             => trans('admin.password_require_number_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.password_require_number_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'password_require_special',
				'label'             => trans('admin.password_require_special_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.password_require_special_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],

			[
				'name'  => 'session_sep',
				'type'  => 'custom_html',
				'value' => '<h3>' . trans('admin.session_settings') . '</h3>',
			],
			[
				'name'              => 'session_timeout',
				'label'             => trans('admin.session_timeout_label'),
				'type'              => 'number',
				'attributes'        => [
					'min'  => 1,
					'max'  => 10080,
					'step' => 1,
				],
				'hint'              => trans('admin.session_timeout_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'single_session',
				'label'             => trans('admin.single_session_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.single_session_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
		];

		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields);
	}
}
