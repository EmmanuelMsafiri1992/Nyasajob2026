<?php
/**
 * Nyasajob - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Observers\Traits\Setting;

use App\Models\Permission;
use App\Models\User;
use App\Notifications\ExampleSms;
use App\Providers\AppService\ConfigTrait\SmsConfig;
use Illuminate\Support\Facades\Notification;
use Prologue\Alerts\Facades\Alert;

trait SmsTrait
{
	use SmsConfig;
	
	/**
	 * Updating
	 *
	 * @param $setting
	 * @param $original
	 * @return bool
	 */
	public function smsUpdating($setting, $original)
	{
		$validateDriverParameters = $setting->value['validate_driver'] ?? false;
		if ($validateDriverParameters) {
			$this->updateSmsConfig($setting->value);
			
			/*
			 * Send Example SMS
			 */
			$driver = $setting->value['driver'] ?? null;
			try {
				if (config('settings.app.phone_number')) {
					Notification::route($driver, config('settings.app.phone_number'))->notify(new ExampleSms());
				} else {
					$admins = User::permission(Permission::getStaffPermissions())->get();
					if ($admins->count() > 0) {
						Notification::send($admins, new ExampleSms());
					}
				}
			} catch (\Throwable $e) {
				$message = $e->getMessage();
				
				if (isAdminPanel()) {
					Alert::error($message)->flash();
				} else {
					flash($message)->error();
				}
				
				return false;
			}
		}
		
		$this->saveParametersInEnvFile($setting);
	}
	
	/**
	 * Save SMS Settings in the /.env file
	 *
	 * @param $setting
	 */
	private function saveParametersInEnvFile($setting)
	{

		if (array_key_exists('vonage_key', $setting->value)) {
			if (!empty($setting->value['vonage_key'])) {
				setEnvValue('VONAGE_KEY', $setting->value['vonage_key']);
			} else {
				if (envKeyExists('VONAGE_KEY')) {
					deleteEnvKey('VONAGE_KEY');
				}
			}
		}
		if (array_key_exists('vonage_secret', $setting->value)) {
			if (!empty($setting->value['vonage_secret'])) {
				setEnvValue('VONAGE_SECRET', $setting->value['vonage_secret']);
			} else {
				if (envKeyExists('VONAGE_SECRET')) {
					deleteEnvKey('VONAGE_SECRET');
				}
			}
		}
		if (array_key_exists('vonage_application_id', $setting->value)) {
			if (!empty($setting->value['vonage_application_id'])) {
				setEnvValue('VONAGE_APPLICATION_ID', $setting->value['vonage_application_id']);
			} else {
				if (envKeyExists('VONAGE_APPLICATION_ID')) {
					deleteEnvKey('VONAGE_APPLICATION_ID');
				}
			}
		}
		if (array_key_exists('vonage_from', $setting->value)) {
			if (!empty($setting->value['vonage_from'])) {
				setEnvValue('VONAGE_SMS_FROM', $setting->value['vonage_from']);
			} else {
				if (envKeyExists('VONAGE_SMS_FROM')) {
					deleteEnvKey('VONAGE_SMS_FROM');
				}
			}
		}
		if (array_key_exists('twilio_username', $setting->value)) {
			if (!empty($setting->value['twilio_username'])) {
				setEnvValue('TWILIO_USERNAME', $setting->value['twilio_username']);
			} else {
				if (envKeyExists('TWILIO_USERNAME')) {
					deleteEnvKey('TWILIO_USERNAME');
				}
			}
		}
		if (array_key_exists('twilio_password', $setting->value)) {
			if (!empty($setting->value['twilio_password'])) {
				setEnvValue('TWILIO_PASSWORD', $setting->value['twilio_password']);
			} else {
				if (envKeyExists('TWILIO_PASSWORD')) {
					deleteEnvKey('TWILIO_PASSWORD');
				}
			}
		}
		if (array_key_exists('twilio_auth_token', $setting->value)) {
			if (!empty($setting->value['twilio_auth_token'])) {
				setEnvValue('TWILIO_AUTH_TOKEN', $setting->value['twilio_auth_token']);
			} else {
				if (envKeyExists('TWILIO_AUTH_TOKEN')) {
					deleteEnvKey('TWILIO_AUTH_TOKEN');
				}
			}
		}
		if (array_key_exists('twilio_account_sid', $setting->value)) {
			if (!empty($setting->value['twilio_account_sid'])) {
				setEnvValue('TWILIO_ACCOUNT_SID', $setting->value['twilio_account_sid']);
			} else {
				if (envKeyExists('TWILIO_ACCOUNT_SID')) {
					deleteEnvKey('TWILIO_ACCOUNT_SID');
				}
			}
		}
		if (array_key_exists('twilio_from', $setting->value)) {
			if (!empty($setting->value['twilio_from'])) {
				setEnvValue('TWILIO_FROM', $setting->value['twilio_from']);
			} else {
				if (envKeyExists('TWILIO_FROM')) {
					deleteEnvKey('TWILIO_FROM');
				}
			}
		}
		if (array_key_exists('twilio_alpha_sender', $setting->value)) {
			if (!empty($setting->value['twilio_alpha_sender'])) {
				setEnvValue('TWILIO_ALPHA_SENDER', $setting->value['twilio_alpha_sender']);
			} else {
				if (envKeyExists('TWILIO_ALPHA_SENDER')) {
					deleteEnvKey('TWILIO_ALPHA_SENDER');
				}
			}
		}
		if (array_key_exists('twilio_sms_service_sid', $setting->value)) {
			if (!empty($setting->value['twilio_sms_service_sid'])) {
				setEnvValue('TWILIO_SMS_SERVICE_SID', $setting->value['twilio_sms_service_sid']);
			} else {
				if (envKeyExists('TWILIO_SMS_SERVICE_SID')) {
					deleteEnvKey('TWILIO_SMS_SERVICE_SID');
				}
			}
		}
		if (array_key_exists('twilio_debug_to', $setting->value)) {
			if (!empty($setting->value['twilio_debug_to'])) {
				setEnvValue('TWILIO_DEBUG_TO', $setting->value['twilio_debug_to']);
			} else {
				if (envKeyExists('TWILIO_DEBUG_TO')) {
					deleteEnvKey('TWILIO_DEBUG_TO');
				}
			}
		}

		// Some time of pause
		sleep(2);
	}
}
