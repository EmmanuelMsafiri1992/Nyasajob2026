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

trait OptimizationTrait
{
	/**
	 * Updating
	 *
	 * @param $setting
	 * @param $original
	 */
	public function optimizationUpdating($setting, $original)
	{
		$this->updateEnvFileForCacheParameters($setting);
	}
	
	/**
	 * Update app caching system parameters in the /.env file
	 *
	 * @param $setting
	 */
	private function updateEnvFileForCacheParameters($setting)
	{
		if (!is_array($setting->value)) return;

		// Remove Existing Variables
		if (envKeyExists('CACHE_DRIVER')) {
			deleteEnvKey('CACHE_DRIVER');
		}
		if (envKeyExists('CACHE_PREFIX')) {
			deleteEnvKey('CACHE_PREFIX');
		}
		if (envKeyExists('MEMCACHED_PERSISTENT_ID')) {
			deleteEnvKey('MEMCACHED_PERSISTENT_ID');
		}
		if (envKeyExists('MEMCACHED_USERNAME')) {
			deleteEnvKey('MEMCACHED_USERNAME');
		}
		if (envKeyExists('MEMCACHED_PASSWORD')) {
			deleteEnvKey('MEMCACHED_PASSWORD');
		}
		$i = 1;
		while (envKeyExists('MEMCACHED_SERVER_' . $i . '_HOST')) {
			deleteEnvKey('MEMCACHED_SERVER_' . $i . '_HOST');
			$i++;
		}
		$i = 1;
		while (envKeyExists('MEMCACHED_SERVER_' . $i . '_PORT')) {
			deleteEnvKey('MEMCACHED_SERVER_' . $i . '_PORT');
			$i++;
		}
		if (envKeyExists('REDIS_CLIENT')) {
			deleteEnvKey('REDIS_CLIENT');
		}
		if (envKeyExists('REDIS_CLUSTER')) {
			deleteEnvKey('REDIS_CLUSTER');
		}
		if (envKeyExists('REDIS_HOST')) {
			deleteEnvKey('REDIS_HOST');
		}
		if (envKeyExists('REDIS_PASSWORD')) {
			deleteEnvKey('REDIS_PASSWORD');
		}
		if (envKeyExists('REDIS_PORT')) {
			deleteEnvKey('REDIS_PORT');
		}
		if (envKeyExists('REDIS_DB')) {
			deleteEnvKey('REDIS_DB');
		}
		$i = 1;
		while (envKeyExists('REDIS_CLUSTER_' . $i . '_HOST')) {
			deleteEnvKey('REDIS_CLUSTER_' . $i . '_HOST');
			$i++;
		}
		$i = 1;
		while (envKeyExists('REDIS_CLUSTER_' . $i . '_PASSWORD')) {
			deleteEnvKey('REDIS_CLUSTER_' . $i . '_PASSWORD');
			$i++;
		}
		$i = 1;
		while (envKeyExists('REDIS_CLUSTER_' . $i . '_PORT')) {
			deleteEnvKey('REDIS_CLUSTER_' . $i . '_PORT');
			$i++;
		}
		$i = 1;
		while (envKeyExists('REDIS_CLUSTER_' . $i . '_DB')) {
			deleteEnvKey('REDIS_CLUSTER_' . $i . '_DB');
			$i++;
		}
		
		// Create Variables
		if (array_key_exists('cache_driver', $setting->value)) {
			setEnvValue('CACHE_DRIVER', $setting->value['cache_driver']);
			setEnvValue('CACHE_PREFIX', 'lc_');
		}
		if (array_key_exists('memcached_persistent_id', $setting->value)) {
			setEnvValue('MEMCACHED_PERSISTENT_ID', $setting->value['memcached_persistent_id']);
		}
		if (array_key_exists('memcached_sasl_username', $setting->value)) {
			setEnvValue('MEMCACHED_USERNAME', $setting->value['memcached_sasl_username']);
		}
		if (array_key_exists('memcached_sasl_password', $setting->value)) {
			setEnvValue('MEMCACHED_PASSWORD', $setting->value['memcached_sasl_password']);
		}
		$i = 1;
		while (
			array_key_exists('memcached_servers_' . $i . '_host', $setting->value)
			&& array_key_exists('memcached_servers_' . $i . '_port', $setting->value)
		) {
			if (envKeyExists('MEMCACHED_SERVER_' . $i . '_HOST')) {
				deleteEnvKey('MEMCACHED_SERVER_' . $i . '_HOST');
			}
			if (envKeyExists('MEMCACHED_SERVER_' . $i . '_PORT')) {
				deleteEnvKey('MEMCACHED_SERVER_' . $i . '_PORT');
			}
			setEnvValue('MEMCACHED_SERVER_' . $i . '_HOST', $setting->value['memcached_servers_' . $i . '_host']);
			setEnvValue('MEMCACHED_SERVER_' . $i . '_PORT', $setting->value['memcached_servers_' . $i . '_port']);
			$i++;
		}
		if (array_key_exists('redis_client', $setting->value)) {
			setEnvValue('REDIS_CLIENT', $setting->value['redis_client']);
		}
		if (array_key_exists('redis_cluster', $setting->value)) {
			setEnvValue('REDIS_CLUSTER', $setting->value['redis_cluster']);
		}
		if (array_key_exists('redis_host', $setting->value)) {
			setEnvValue('REDIS_HOST', $setting->value['redis_host']);
		}
		if (array_key_exists('redis_password', $setting->value)) {
			setEnvValue('REDIS_PASSWORD', $setting->value['redis_password']);
		}
		if (array_key_exists('redis_port', $setting->value)) {
			setEnvValue('REDIS_PORT', $setting->value['redis_port']);
		}
		if (array_key_exists('redis_database', $setting->value)) {
			setEnvValue('REDIS_DB', $setting->value['redis_database']);
		}
		if (array_key_exists('redis_cluster_activation', $setting->value) && $setting->value['redis_cluster_activation'] == '1') {
			$i = 1;
			while (
				array_key_exists('redis_cluster_' . $i . '_host', $setting->value)
				&& array_key_exists('redis_cluster_' . $i . '_password', $setting->value)
				&& array_key_exists('redis_cluster_' . $i . '_port', $setting->value)
				&& array_key_exists('redis_cluster_' . $i . '_database', $setting->value)
			) {
				setEnvValue('REDIS_CLUSTER_' . $i . '_HOST', $setting->value['redis_cluster_' . $i . '_host']);
				setEnvValue('REDIS_CLUSTER_' . $i . '_PASSWORD', $setting->value['redis_cluster_' . $i . '_password']);
				setEnvValue('REDIS_CLUSTER_' . $i . '_PORT', $setting->value['redis_cluster_' . $i . '_port']);
				setEnvValue('REDIS_CLUSTER_' . $i . '_DB', $setting->value['redis_cluster_' . $i . '_database']);
				$i++;
			}
		}

		// Some time of pause
		sleep(2);
	}
}
