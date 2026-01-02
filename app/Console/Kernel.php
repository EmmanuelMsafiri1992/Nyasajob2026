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

namespace App\Console;

use App\Helpers\Date;
use Illuminate\Console\Scheduling\Schedule;

class Kernel
{
	public function __invoke(Schedule $schedule): void
	{
		$tz = Date::getAppTimeZone();
		
		// Deleting Expired Tokens (Resetting Password)
		// Doc: https://laravel.com/docs/11.x/passwords
		$schedule->command('auth:clear-resets')->timezone($tz)->everyFifteenMinutes();
		
		// Clear Listings
		$schedule->command('listings:purge')->timezone($tz)->hourly();
		
		// Backups
		setBackupConfig();
		$disableNotifications = (config('settings.backup.disable_notifications')) ? ' --disable-notifications' : '';
		
		// Taking Backups
		$takingBackup = config('settings.backup.taking_backup');
		if ($takingBackup != 'none') {
			$takingBackupAt = config('settings.backup.taking_backup_at');
			$takingBackupAt = ($takingBackupAt != '') ? $takingBackupAt : '00:00';
			
			if ($takingBackup == 'daily') {
				$schedule->command('backup:run' . $disableNotifications)->timezone($tz)->dailyAt($takingBackupAt);
			}
			if ($takingBackup == 'weekly') {
				$schedule->command('backup:run' . $disableNotifications)->timezone($tz)->weeklyOn(1, $takingBackupAt);
			}
			if ($takingBackup == 'monthly') {
				$schedule->command('backup:run' . $disableNotifications)->timezone($tz)->monthlyOn(1, $takingBackupAt);
			}
			if ($takingBackup == 'yearly') {
				$schedule->command('backup:run' . $disableNotifications)->timezone($tz)->yearlyOn(1, 1, $takingBackupAt);
			}
			
			// Cleaning Up Old Backups
			$schedule->command('backup:clean' . $disableNotifications)->timezone($tz)->daily();
		}
		
		// Clear Cache & Views
		if (!env('DISABLE_CACHE_AUTO_CLEAR') || (int)env('DISABLE_CACHE_AUTO_CLEAR', 0) != 1) {
			$schedule->command('cache:clear')->timezone($tz)->weeklyOn(7, '6:00');
			$schedule->command('cache:clear')->timezone($tz)->weeklyOn(7, '6:05'); // To prevent file lock issues (Optional)
			$schedule->command('view:clear')->timezone($tz)->weeklyOn(7, '6:00');
		}

		// Send Daily Job Digest Notifications
		$schedule->command('jobs:send-daily-digest')->timezone($tz)->dailyAt('08:00');

		// =============================================
		// RSS Feed Job Aggregation Scheduled Commands
		// =============================================

		// Fetch RSS feeds every 3 hours (staggered to avoid peak traffic)
		// Limit to 10 sources per run to prevent server overload
		$schedule->command('rss:fetch --limit=10')
			->timezone($tz)
			->everyThreeHours()
			->withoutOverlapping()
			->runInBackground();

		// Process staged items every 4 hours
		// Clean descriptions, resolve locations, infer categories
		$schedule->command('rss:process --limit=200')
			->timezone($tz)
			->everyFourHours()
			->withoutOverlapping()
			->runInBackground();

		// Import approved items every 2 hours with auto-approve
		// This imports jobs directly without manual review
		$schedule->command('rss:import --limit=100 --auto-approve')
			->timezone($tz)
			->everyTwoHours()
			->withoutOverlapping()
			->runInBackground();

		// Weekly cleanup - remove old staged items and logs
		// Run on Sundays at 4:00 AM to minimize impact
		$schedule->command('rss:cleanup --days=30 --log-days=90')
			->timezone($tz)
			->weeklyOn(0, '04:00')
			->runInBackground();
	}
}
