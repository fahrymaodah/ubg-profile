<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ⚠️ TESTING ONLY - REMOVE BEFORE PRODUCTION ⚠️
 * 
 * This controller provides authentication bypass for testing purposes.
 * It should be removed or disabled before deploying to production.
 * 
 * @see docs/TODO.md for cleanup instructions
 */
class TestAuthController extends Controller
{
    /**
     * Auto-login as superadmin for testing
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAsSuperAdmin()
    {
        if (!$this->isTestingEnvironment()) {
            abort(403, 'This feature is only available in testing environment');
        }

        $user = User::where('email', 'superadmin@ubg.ac.id')->first();

        if (!$user) {
            return redirect('/admin/login')->with('error', 'Superadmin user not found. Please run: php artisan db:seed');
        }

        Auth::login($user);

        return redirect('/admin');
    }

    /**
     * Auto-login as regular admin for testing
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAsAdmin()
    {
        if (!$this->isTestingEnvironment()) {
            abort(403, 'This feature is only available in testing environment');
        }

        $user = User::where('email', 'admin@ubg.ac.id')->first();

        if (!$user) {
            return redirect('/admin/login')->with('error', 'Admin user not found. Please run: php artisan db:seed');
        }

        Auth::login($user);

        return redirect('/admin');
    }

    /**
     * Login as specific user by ID (for testing different roles)
     * 
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAsUser(int $userId)
    {
        if (!$this->isTestingEnvironment()) {
            abort(403, 'This feature is only available in testing environment');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect('/admin/login')->with('error', 'User not found');
        }

        Auth::login($user);

        return redirect('/admin');
    }

    /**
     * Logout and redirect to login page
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        
        return redirect('/admin/login');
    }

    /**
     * Show testing dashboard with quick login options
     * 
     * @return \Illuminate\View\View
     */
    public function testingDashboard()
    {
        if (!$this->isTestingEnvironment()) {
            abort(403, 'This feature is only available in testing environment');
        }

        $users = User::all();

        return view('testing.dashboard', compact('users'));
    }

    /**
     * Check if the application is in testing/local environment
     * 
     * @return bool
     */
    protected function isTestingEnvironment(): bool
    {
        return in_array(app()->environment(), ['local', 'testing', 'development']);
    }
}
