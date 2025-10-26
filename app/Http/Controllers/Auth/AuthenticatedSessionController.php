<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            // 1. validated data

            $data = $request->validated();
            if (($data['admin'] ?? null) !== 'skip') {
                return view('dashboard.buzzle.buzzle-card');
            }

            // 2. authenticate first
            $request->authenticate();

            // 3. regenerate session
            $request->session()->regenerate();

            // 4. save login history
            AdminLoginHistory::logLogin($data['email'], $data);
            
            // 5. redirect
            return redirect()->intended(RouteServiceProvider::HOME);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $sessionId = $request->session()->getId();
      
        // ? Log The Logout Time
        AdminLoginHistory::logLogout($sessionId);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dashboard');
    }
}
