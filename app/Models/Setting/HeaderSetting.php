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
 * settings.header.option
 */

class HeaderSetting
{
	public static function getValues($value, $disk)
	{
		if (empty($value)) {

			$value['show_country_flag'] = '1';
			$value['show_language_selector'] = '1';
			$value['show_post_job_button'] = '1';

		} else {

			if (!array_key_exists('show_country_flag', $value)) {
				$value['show_country_flag'] = '1';
			}
			if (!array_key_exists('show_language_selector', $value)) {
				$value['show_language_selector'] = '1';
			}
			if (!array_key_exists('show_post_job_button', $value)) {
				$value['show_post_job_button'] = '1';
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
				'name'  => 'header_visibility_separator',
				'type'  => 'custom_html',
				'value' => '<h4 class="mb-3">' . trans('admin.header_visibility_options') . '</h4>',
			],
			[
				'name'              => 'show_country_flag',
				'label'             => trans('admin.show_country_flag'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.show_country_flag_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'show_language_selector',
				'label'             => trans('admin.show_language_selector'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.show_language_selector_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'show_post_job_button',
				'label'             => trans('admin.show_post_job_button'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.show_post_job_button_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'hide_auth_links',
				'label'             => trans('admin.hide_auth_links'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.hide_auth_links_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'topbar_separator',
				'type'  => 'custom_html',
				'value' => '<h4 class="mb-3 mt-4">' . trans('admin.topbar_options') . '</h4>',
			],
			[
				'name'              => 'show_topbar',
				'label'             => trans('admin.show_topbar'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.show_topbar_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-12',
				],
			],
			[
				'name'              => 'topbar_phone',
				'label'             => trans('admin.topbar_phone'),
				'type'              => 'text',
				'hint'              => trans('admin.topbar_phone_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6 topbar-field',
				],
			],
			[
				'name'              => 'topbar_email',
				'label'             => trans('admin.topbar_email'),
				'type'              => 'email',
				'hint'              => trans('admin.topbar_email_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6 topbar-field',
				],
			],
			[
				'name'              => 'topbar_text',
				'label'             => trans('admin.topbar_text'),
				'type'              => 'text',
				'hint'              => trans('admin.topbar_text_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-12 topbar-field',
				],
			],
		];

		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields);
	}
}
