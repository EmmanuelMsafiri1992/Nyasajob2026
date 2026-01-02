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

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Storage file redirect for logo images
Route::get('storage/app/default/{filename}', function ($filename) {
    $publicPath = public_path('app/default/' . $filename);
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }
    abort(404);
});

Route::middleware(['installed'])
	->group(function () {
		// admin
		$prefix = config('larapen.admin.route', 'admin');
		Route::namespace('Admin')->prefix($prefix)->group(__DIR__ . '/web/admin.php');

		// public
		Route::namespace('Public')->group(__DIR__ . '/web/public.php');

		// CUSTOM ROUTES (Controllers without Public namespace)
		// LEARNING MANAGEMENT SYSTEM (LMS) - Courses
		Route::prefix('courses')->group(function () {
			Route::get('/', [\App\Http\Controllers\Web\CourseController::class, 'index'])->name('courses.index');
			Route::get('/{slug}', [\App\Http\Controllers\Web\CourseController::class, 'show'])->name('courses.show');
			Route::middleware('auth')->group(function () {
				Route::get('/my/enrolled', [\App\Http\Controllers\Web\CourseController::class, 'myCourses'])->name('courses.my');
				Route::post('/{courseId}/enroll', [\App\Http\Controllers\Web\EnrollmentController::class, 'enroll'])->name('courses.enroll');
				Route::delete('/{courseId}/unenroll', [\App\Http\Controllers\Web\EnrollmentController::class, 'unenroll'])->name('courses.unenroll');
				Route::get('/{courseSlug}/lessons/{lessonId}', [\App\Http\Controllers\Web\LessonController::class, 'show'])->name('courses.lessons.show');
				Route::post('/lessons/{lessonId}/complete', [\App\Http\Controllers\Web\LessonController::class, 'complete'])->name('courses.lessons.complete');
				Route::get('/{courseSlug}/lessons/{lessonId}/interactive', [\App\Http\Controllers\Web\LessonController::class, 'interactive'])->name('courses.lessons.interactive');
			});
		});

		// PRODUCT ADS TRACKING
		Route::prefix('ad')->group(function () {
			Route::get('click/{id}', [\App\Http\Controllers\Web\ProductAdController::class, 'click'])->where('id', '[0-9]+');
			Route::post('impression/{id}', [\App\Http\Controllers\Web\ProductAdController::class, 'impression'])->where('id', '[0-9]+');
		});

		// ADVERTISE WITH US
		Route::prefix('advertise')->group(function () {
			Route::get('/', [\App\Http\Controllers\Web\AdvertiseController::class, 'index'])->name('advertise.index');
			Route::get('/create/{packageId}', [\App\Http\Controllers\Web\AdvertiseController::class, 'create'])->name('advertise.create')->where('packageId', '[0-9]+');
			Route::post('/store', [\App\Http\Controllers\Web\AdvertiseController::class, 'store'])->name('advertise.store');
			Route::get('/payment/{adId}/{packageId}', [\App\Http\Controllers\Web\AdvertiseController::class, 'payment'])->name('advertise.payment')->where(['adId' => '[0-9]+', 'packageId' => '[0-9]+']);
			Route::post('/payment/callback', [\App\Http\Controllers\Web\AdvertiseController::class, 'paymentCallback'])->name('advertise.payment.callback');
			Route::get('/my-ads', [\App\Http\Controllers\Web\AdvertiseController::class, 'myAds'])->name('advertise.my-ads');
			Route::post('/{id}/pause', [\App\Http\Controllers\Web\AdvertiseController::class, 'pause'])->name('advertise.pause')->where('id', '[0-9]+');
			Route::post('/{id}/resume', [\App\Http\Controllers\Web\AdvertiseController::class, 'resume'])->name('advertise.resume')->where('id', '[0-9]+');
		});
	});
