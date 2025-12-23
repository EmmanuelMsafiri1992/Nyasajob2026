<?php
namespace App\Http\Controllers\Api\User;

use App\Models\Company;
use App\Models\JobMatch;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Resume;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Thread;
use App\Models\UserJobPreference;
use Illuminate\Support\Facades\DB;

trait Stats
{
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getStats($id): \Illuminate\Http\JsonResponse
	{
		$posts = [];
		$threads = [];
		
		// posts (published)
		$posts['published'] = Post::whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->verified()
			->unarchived()
			->reviewed()
			->count();
		
		// posts (pendingApproval)
		$posts['pendingApproval'] = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
			->whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->unverified()
			->count();
		
		// posts (archived)
		$posts['archived'] = Post::whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->archived()
			->count();
		
		// posts (visits)
		$postsVisits = DB::table((new Post())->getTable())
			->select('user_id', DB::raw('SUM(visits) as totalVisits'))
			->where('country_code', config('country.code'))
			->where('user_id', $id)
			->groupBy('user_id')
			->first();
		$posts['visits'] = $postsVisits->totalVisits ?? 0;
		
		// posts (favourite)
		$posts['favourite'] = SavedPost::whereHas('post', function ($query) {
			$query->whereHas('country')
				->currentCountry();
		})->where('user_id', $id)
			->count();
		
		// savedSearch
		$savedSearch = SavedSearch::whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->count();
		
		// threads (all)
		$threads['all'] = Thread::whereHas('post', function ($query) {
			$query->whereHas('country')
				->currentCountry()
				->unarchived();
		})->forUser($id)->count();
		
		// threads (withNewMessage)
		$threads['withNewMessage'] = Thread::whereHas('post', function ($query) {
			$query->whereHas('country')
				->currentCountry()
				->unarchived();
		})->forUserWithNewMessages($id)->count();
		
		// transactions (payments)
		$transactions = Payment::whereHas('post', function ($query) use ($id) {
			$query->whereHas('country')
				->currentCountry()
				->whereHas('user', function ($query) use ($id) {
					$query->where('user_id', $id);
				});
		})->whereHas('package', function ($query) {
			$query->whereHas('currency');
		})->count();
		
		// companies
		$companies = Company::where('user_id', $id)->count();
		
		// resumes
		$resumes = Resume::where('user_id', $id)->count();

		// jobPreferences
		$jobPreferences = UserJobPreference::where('user_id', $id)->count();

		// jobMatches
		$jobMatches = JobMatch::where('user_id', $id)->count();

		// stats
		$stats = [
			'posts'          => $posts,
			'savedSearch'    => $savedSearch,
			'threads'        => $threads,
			'transactions'   => $transactions,
			'companies'      => $companies,
			'resumes'        => $resumes,
			'jobPreferences' => $jobPreferences,
			'jobMatches'     => $jobMatches,
		];
		
		$data = [
			'success' => true,
			'message' => null,
			'result'  => $stats,
		];
		
		return $this->apiResponse($data);
	}
}
