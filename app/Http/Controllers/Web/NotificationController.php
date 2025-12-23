<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class NotificationController extends Controller
{
    /**
     * Show unsubscribe confirmation page (public access with encoded email)
     *
     * @param string $encodedEmail
     * @return \Illuminate\View\View
     */
    public function unsubscribe($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            $user = User::where('email', $email)->first();

            if (!$user) {
                return view('account.notifications.unsubscribe-error', [
                    'error' => 'User not found'
                ]);
            }

            // SEO
            MetaTag::set('title', 'Unsubscribe from Job Notifications');
            MetaTag::set('description', 'Manage your job notification preferences');

            return view('account.notifications.unsubscribe', [
                'user' => $user,
                'encodedEmail' => $encodedEmail
            ]);
        } catch (\Exception $e) {
            return view('account.notifications.unsubscribe-error', [
                'error' => 'Invalid unsubscribe link'
            ]);
        }
    }

    /**
     * Process unsubscribe request
     *
     * @param Request $request
     * @param string $encodedEmail
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processUnsubscribe(Request $request, $encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            $user = User::where('email', $email)->first();

            if (!$user) {
                return redirect()->route('home')
                    ->with(['error' => 'User not found']);
            }

            $user->job_notification_enabled = false;
            $user->save();

            return view('account.notifications.unsubscribe-success', [
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with(['error' => 'Failed to unsubscribe. Please try again.']);
        }
    }

    /**
     * Show notification settings page (authenticated users)
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with(['message' => 'Please login to access notification settings.']);
        }

        $user = Auth::user();

        // SEO
        MetaTag::set('title', 'Notification Settings');
        MetaTag::set('description', 'Manage your job notification preferences');

        return view('account.notifications.settings', [
            'user' => $user
        ]);
    }

    /**
     * Toggle job notification setting
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleNotifications(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $user->job_notification_enabled = $request->input('enabled', false) ? true : false;
        $user->save();

        $message = $user->job_notification_enabled
            ? 'Job notifications have been enabled. You will receive emails when new jobs are posted in your country.'
            : 'Job notifications have been disabled. You will no longer receive job posting emails.';

        return redirect()->back()
            ->with(['success' => $message]);
    }
}
