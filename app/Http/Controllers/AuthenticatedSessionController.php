<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Log successful login
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_time' => now()
                ])
                ->log('User logged in successfully');

            return redirect()->intended('/');
        }

        // Log failed login attempt
        activity()
            ->withProperties([
                'username' => $credentials['username'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempt_time' => now()
            ])
            ->log('Failed login attempt');

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function destroy(Request $request)
    {
        // Log logout before actually logging out
        if (Auth::check()) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'logout_time' => now()
                ])
                ->log('User logged out');
        }

        // Clear authentication
        Auth::guard('web')->logout();

        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // Flush all session data
        $request->session()->flush();

        // Redirect with cache control headers to prevent back button issues
        return redirect('/login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
    }
}
