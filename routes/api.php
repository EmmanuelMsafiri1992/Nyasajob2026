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

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialController;
use App\Http\Controllers\Api\CaptchaController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\GenderController;
use App\Http\Controllers\Api\HomeSectionController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostTypeController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ReportTypeController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\Api\SalaryTypeController;
use App\Http\Controllers\Api\SavedPostController;
use App\Http\Controllers\Api\SavedSearchController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SubAdmin1Controller;
use App\Http\Controllers\Api\SubAdmin2Controller;
use App\Http\Controllers\Api\ThreadController;
use App\Http\Controllers\Api\ThreadMessageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserTypeController;
use App\Http\Controllers\Api\WorkerProfileController;
use App\Http\Controllers\Api\WorkerSkillController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// auth
Route::namespace('Auth')
	->group(function ($router) {
		
		Route::prefix('auth')
			->group(function ($router) {
				$router->pattern('userId', '[0-9]+');
				
				Route::controller(LoginController::class)
					->group(function ($router) {
						Route::post('login', 'login')->name('auth.login');
						Route::get('logout/{userId}', 'logout')->name('auth.logout');
					});
				
				Route::controller(ForgotPasswordController::class)
					->group(function ($router) {
						Route::post('password/email', 'sendResetLink')->name('auth.password.email');
					});
				
				Route::controller(ResetPasswordController::class)
					->group(function ($router) {
						Route::post('password/token', 'sendResetToken')->name('auth.password.token');
						Route::post('password/reset', 'reset')->name('auth.password.reset');
					});
				
				Route::controller(SocialController::class)
					->group(function ($router) {
						$router->pattern('provider', 'facebook|linkedin|twitter-oauth-2|google');
						Route::get('{provider}', 'getProviderTargetUrl');
						Route::get('{provider}/callback', 'handleProviderCallback');
					});
			});
		
		Route::controller(ForgotPasswordController::class)
			->group(function ($router) {
				// password - Email Address or Phone Number verification
				$router->pattern('field', 'email|phone');
				$router->pattern('token', '.*');
				Route::get('password/{id}/verify/resend/email', 'reSendEmailVerification'); // Not implemented
				Route::get('password/{id}/verify/resend/sms', 'reSendPhoneVerification');   // Not implemented
				Route::get('password/verify/{field}/{token?}', 'verification');
			});
		
	});

// genders
Route::prefix('genders')
	->controller(GenderController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('/', 'index')->name('genders.index');
		Route::get('{id}', 'show')->name('genders.show');
	});

// postTypes
Route::prefix('postTypes')
	->controller(PostTypeController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('/', 'index')->name('postTypes.index');
		Route::get('{id}', 'show')->name('postTypes.show');
	});

// reportTypes
Route::prefix('reportTypes')
	->controller(ReportTypeController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('/', 'index')->name('reportTypes.index');
		Route::get('{id}', 'show')->name('reportTypes.show');
	});

// salaryTypes
Route::prefix('salaryTypes')
	->controller(SalaryTypeController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('/', 'index')->name('salaryTypes.index');
		Route::get('{id}', 'show')->name('salaryTypes.show');
	});

// userTypes
Route::prefix('userTypes')
	->controller(UserTypeController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('/', 'index')->name('userTypes.index');
		Route::get('{id}', 'show')->name('userTypes.show');
	});


// categories
Route::prefix('categories')
	->controller(CategoryController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		$router->pattern('slugOrId', '[^/]+');
		Route::get('/', 'index')->name('categories.index');
		Route::get('{slugOrId}', 'show')->name('categories.show');
	});

// countries
Route::prefix('countries')
	->group(function ($router) {
		Route::controller(CountryController::class)
			->group(function ($router) {
				$router->pattern('code', '[a-zA-Z]{2}');
				Route::get('/', 'index')->name('countries.index');
				Route::get('{code}', 'show')->name('countries.show');
			});
		
		$router->pattern('countryCode', '[a-zA-Z]{2}');
		Route::get('{countryCode}/subAdmins1', [SubAdmin1Controller::class, 'index'])->name('subAdmins1.index');
		Route::get('{countryCode}/subAdmins2', [SubAdmin2Controller::class, 'index'])->name('subAdmins2.index');
		Route::get('{countryCode}/cities', [CityController::class, 'index'])->name('cities.index');
	});

// subAdmins1
Route::prefix('subAdmins1')
	->controller(SubAdmin1Controller::class)
	->group(function ($router) {
		$router->pattern('code', '[^/]+');
		Route::get('{code}', 'show')->name('subAdmins1.show');
	});

// subAdmins2
Route::prefix('subAdmins2')
	->controller(SubAdmin2Controller::class)
	->group(function ($router) {
		$router->pattern('code', '[^/]+');
		Route::get('{code}', 'show')->name('subAdmins2.show');
	});

// cities
Route::prefix('cities')
	->controller(CityController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('{id}', 'show')->name('cities.show');
	});

// users
Route::prefix('users')
	->controller(UserController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		
		Route::get('/', 'index')->name('users.index');
		Route::get('{id}', 'show')->name('users.show');
		Route::post('/', 'store')->name('users.store');
		Route::middleware(['auth:sanctum'])->group(function ($router) {
			Route::get('{id}/stats', 'stats')->name('users.stats');
			
			// Removal (fake deletion) of the user's photo
			// Note: The user's photo is stored as a file path in a column instead of entry row.
			// So the HTTP's GET method can be used to empty the photo column and its file.
			Route::get('{id}/photo/delete', 'removePhoto')->name('users.photo.delete');
			Route::put('{id}/photo', 'updatePhoto')->name('users.photo.update');
			Route::put('{id}/dark-mode', 'setDarkMode')->name('users.darkMode.update');
			
			// Update User (with its photo)
			Route::put('{id}', 'update')->name('users.update');
		});
		Route::delete('{id}', 'destroy')->name('users.destroy');
		
		// users - Email Address or Phone Number verification
		$router->pattern('field', 'email|phone');
		$router->pattern('token', '.*');
		Route::get('{id}/verify/resend/email', 'reSendEmailVerification');
		Route::get('{id}/verify/resend/sms', 'reSendPhoneVerification');
		Route::get('verify/{field}/{token?}', 'verification');
	});

// companies
Route::prefix('companies')
	->controller(CompanyController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		
		Route::get('/', 'index')->name('companies.index');
		Route::get('{id}', 'show')->name('companies.show');
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				$router->pattern('id', '[0-9]+');
				$router->pattern('ids', '[0-9,]+');
				
				Route::post('/', 'store')->name('companies.store');
				Route::put('{id}', 'update')->name('companies.update');
				Route::delete('{ids}', 'destroy')->name('companies.destroy');
			});
	});

// resumes
Route::prefix('resumes')
	->controller(ResumeController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		
		Route::get('/', 'index')->name('resumes.index');
		Route::get('{id}', 'show')->name('resumes.show');
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				$router->pattern('id', '[0-9]+');
				$router->pattern('ids', '[0-9,]+');
				
				Route::post('/', 'store')->name('resumes.store');
				Route::put('{id}', 'update')->name('resumes.update');
				Route::delete('{ids}', 'destroy')->name('resumes.destroy');
			});
	});

// workerSkills
Route::prefix('workerSkills')
	->controller(WorkerSkillController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('/', 'index')->name('workerSkills.index');
		Route::get('{id}', 'show')->name('workerSkills.show');
	});

// workerProfiles
Route::prefix('workerProfiles')
	->controller(WorkerProfileController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');

		Route::get('/', 'index')->name('workerProfiles.index');
		Route::get('{id}', 'show')->name('workerProfiles.show');
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				Route::post('/', 'store')->name('workerProfiles.store');
				Route::put('{id}', 'update')->name('workerProfiles.update');
				Route::delete('{id}', 'destroy')->name('workerProfiles.destroy');
				Route::post('toggle-visibility', 'toggleVisibility')->name('workerProfiles.toggleVisibility');
			});
	});

// posts
Route::prefix('posts')
	->controller(PostController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');

		Route::get('/', 'index')->name('posts.index');
		Route::get('{id}', 'show')->name('posts.show');
		Route::post('/', 'store')->name('posts.store');
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				$router->pattern('ids', '[0-9,]+');
				Route::put('{id}/offline', 'offline')->name('posts.offline');
				Route::put('{id}/repost', 'repost')->name('posts.repost');
				Route::put('{id}', 'update')->name('posts.update');
				Route::delete('{ids}', 'destroy')->name('posts.destroy');
			});

		// listings - Email Address or Phone Number verification
		$router->pattern('field', 'email|phone');
		$router->pattern('token', '.*');
		Route::get('{id}/verify/resend/email', 'reSendEmailVerification');
		Route::get('{id}/verify/resend/sms', 'reSendPhoneVerification');
		Route::get('verify/{field}/{token?}', 'verification');
	});

// savedPosts
Route::prefix('savedPosts')
	->controller(SavedPostController::class)
	->group(function ($router) {
		Route::post('/', 'store')->name('savedPosts.store');
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				$router->pattern('ids', '[0-9,]+');
				Route::get('/', 'index')->name('savedPosts.index');
				Route::delete('{ids}', 'destroy')->name('savedPosts.destroy');
			});
	});

// savedSearches
Route::prefix('savedSearches')
	->controller(SavedSearchController::class)
	->group(function ($router) {
		Route::post('/', 'store')->name('savedSearches.store');
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				$router->pattern('id', '[0-9]+');
				$router->pattern('ids', '[0-9,]+');
				Route::get('/', 'index')->name('savedSearches.index');
				Route::get('{id}', 'show')->name('savedSearches.show');
				Route::delete('{ids}', 'destroy')->name('savedSearches.destroy');
			});
	});

// packages (promotion|subscription)
Route::prefix('packages')
	->controller(PackageController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::get('promotion', 'index')->name('packages.promotion.index');
		Route::get('subscription', 'index')->name('packages.subscription.index');
		Route::get('{id}', 'show')->name('packages.show');
	});

// paymentMethods
Route::prefix('paymentMethods')
	->controller(PaymentMethodController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9a-z]+');
		Route::get('/', 'index')->name('paymentMethods.index');
		Route::get('{id}', 'show')->name('paymentMethods.show');
	});

// payments (promotion|subscription)
Route::prefix('payments')
	->controller(PaymentController::class)
	->group(function ($router) {
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				// promotion
				Route::prefix('promotion')
					->group(function ($router) {
						Route::get('/', 'index')->name('payments.promotion.index');
						
						Route::prefix('posts')
							->group(function ($router) {
								$router->pattern('postId', '[0-9]+');
								Route::get('{postId}/payments', 'index')->name('posts.payments');
							});
					});
				
				// subscription
				Route::prefix('subscription')
					->group(function ($router) {
						Route::get('/', 'index')->name('payments.subscription.index');
						
						Route::prefix('users')
							->group(function ($router) {
								$router->pattern('userId', '[0-9]+');
								Route::get('{userId}/payments', 'index')->name('users.payments');
							});
					});
				
				// show
				$router->pattern('id', '[0-9]+');
				Route::get('{id}', 'show')->name('payments.show');
			});
		
		Route::post('/', 'store')->name('payments.store');
	});

// threads
Route::prefix('threads')
	->group(function ($router) {
		Route::post('/', [ThreadController::class, 'store'])->name('threads.store');
		
		Route::middleware(['auth:sanctum'])
			->group(function ($router) {
				Route::controller(ThreadController::class)
					->group(function ($router) {
						$router->pattern('id', '[0-9]+');
						$router->pattern('ids', '[0-9,]+');
						
						Route::get('/', 'index')->name('threads.index');
						Route::get('{id}', 'show')->name('threads.show');
						Route::put('{id}', 'update')->name('threads.update');
						Route::delete('{ids}', 'destroy')->name('threads.destroy');
						
						Route::post('bulkUpdate/{ids?}', 'bulkUpdate')->name('threads.bulkUpdate'); // Bulk Update
					});
				
				// threadMessages
				Route::controller(ThreadMessageController::class)
					->group(function ($router) {
						$router->pattern('id', '[0-9]+');
						$router->pattern('threadId', '[0-9]+');
						Route::get('{threadId}/messages', 'index')->name('threadMessages.index');
						Route::get('{threadId}/messages/{id}', 'show')->name('threadMessages.show');
					});
			});
	});

// pages
Route::prefix('pages')
	->controller(PageController::class)
	->group(function ($router) {
		$router->pattern('slugOrId', '[^/]+');
		Route::get('/', 'index')->name('pages.index');
		Route::get('{slugOrId}', 'show')->name('pages.show');
	});

// contact
Route::prefix('contact')
	->controller(ContactController::class)
	->group(function ($router) {
		Route::post('/', 'sendForm')->name('contact');
	});
Route::prefix('posts')
	->controller(ContactController::class)
	->group(function ($router) {
		$router->pattern('id', '[0-9]+');
		Route::post('{id}/report', 'sendReport')->name('posts.report');
		Route::post('{id}/sendByEmail', 'sendPostByEmail')->name('posts.sendByEmail');
	});

// languages
Route::prefix('languages')
	->controller(LanguageController::class)
	->group(function ($router) {
		$router->pattern('code', '[^/]+');
		Route::get('/', 'index')->name('languages.index');
		Route::get('{code}', 'show')->name('languages.show');
	});

// settings
Route::prefix('settings')
	->controller(SettingController::class)
	->group(function ($router) {
		$router->pattern('key', '[^/]+');
		Route::get('/', 'index')->name('settings.index');
		Route::get('{key}', 'show')->name('settings.show');
	});

// homeSections - Cached for better performance
Route::prefix('homeSections')
	->controller(HomeSectionController::class)
	->middleware(['cache.headers:public;max_age=3600;etag'])
	->group(function ($router) {
		$router->pattern('method', '[^/]+');
		Route::get('/', 'index')->name('homeSections.index');
		Route::get('{method}', 'show')->name('homeSections.show');
	});

// captcha
Route::prefix('captcha')
	->controller(CaptchaController::class)
	->group(function ($router) {
		Route::get('/', 'getCaptcha')->name('captcha.getCaptcha');
	});

// Salary Calculator API
Route::prefix('salary-calculator')
	->controller(\App\Http\Controllers\Api\SalaryCalculatorApiController::class)
	->group(function ($router) {
		Route::post('/', 'calculate')->name('salary.calculate');
		Route::post('compare-locations', 'compareLocations')->name('salary.compareLocations');
		Route::get('job-titles', 'popularJobTitles')->name('salary.jobTitles');
		Route::get('trends', 'salaryTrends')->name('salary.trends');
		Route::post('submit-data', 'submitSalaryData')->name('salary.submitData');
	});

// Career Assessment API  
Route::prefix('career-assessment')
	->group(function ($router) {
		Route::get('/', [\App\Http\Controllers\Api\CareerAssessmentApiController::class, 'index'])->name('assessment.index');
		Route::get('{type}', [\App\Http\Controllers\Api\CareerAssessmentApiController::class, 'show'])->name('assessment.show');
		Route::post('submit', [\App\Http\Controllers\Api\CareerAssessmentApiController::class, 'submit'])->name('assessment.submit');
		Route::get('results/{id}', [\App\Http\Controllers\Api\CareerAssessmentApiController::class, 'results'])->name('assessment.results');
	});

// Enhanced Subscriptions API
Route::prefix('subscriptions')
	->controller(SubscriptionController::class)
	->group(function ($router) {
		Route::get('/', 'index')->name('subscriptions.index');
		Route::get('analytics', 'analytics')->name('subscriptions.analytics');
		Route::middleware(['auth:sanctum'])->group(function ($router) {
			Route::post('/', 'store')->name('subscriptions.store');
			Route::get('current', 'current')->name('subscriptions.current');
			Route::put('current', 'update')->name('subscriptions.update');
			Route::delete('current', 'cancel')->name('subscriptions.cancel');
			Route::get('usage-stats', 'usageStats')->name('subscriptions.usageStats');
			Route::post('check-feature', 'checkFeature')->name('subscriptions.checkFeature');
		});
	});

// Candidate Scoring API
Route::prefix('candidate-scoring')
	->middleware(['auth:sanctum'])
	->group(function ($router) {
		Route::get('my-score', [\App\Http\Controllers\Api\CandidateScoringApiController::class, 'myScore'])->name('scoring.myScore');
		Route::get('improvement-tips', [\App\Http\Controllers\Api\CandidateScoringApiController::class, 'improvementTips'])->name('scoring.tips');
		Route::post('update-score', [\App\Http\Controllers\Api\CandidateScoringApiController::class, 'updateScore'])->name('scoring.update');
		Route::get('leaderboard', [\App\Http\Controllers\Api\CandidateScoringApiController::class, 'leaderboard'])->name('scoring.leaderboard');
	});

// Coupon Validation API
Route::prefix('coupon')
	->controller(\App\Http\Controllers\Api\CouponController::class)
	->group(function ($router) {
		Route::post('validate', 'validate')->name('coupon.validate');
		Route::post('remove', 'remove')->name('coupon.remove');
	});

// fallback
// catch all routes where the path does not start with 'plugins'
// regex: ^(?!plugins).*$
Route::any('{any}', function () {
	return response()->json([
		'success' => false,
		'message' => 'API endpoint not found.',
	], 404);
})->where('any', '^(?!plugins).*$')->name('any.other');
