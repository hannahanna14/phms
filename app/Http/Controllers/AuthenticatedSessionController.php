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

        // Logout any existing session first
        if (Auth::check()) {
            Auth::logout();
        }

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

            // Always redirect to dashboard after login
            return redirect('/');
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
        // Store user ID for async logging (before logout)
        $userId = Auth::id();
        $ipAddress = $request->ip();
        
        // Clear authentication immediately
        Auth::guard('web')->logout();
        
        // Flush session data immediately without full invalidation
        $request->session()->flush();
        $request->session()->regenerateToken();
        
        // Log logout asynchronously (after response is sent) - only if needed
        if ($userId && config('app.log_user_activity', false)) {
            dispatch(function () use ($userId, $ipAddress) {
                try {
                    activity()
                        ->causedBy($userId)
                        ->withProperties([
                            'ip_address' => $ipAddress,
                            'logout_time' => now()
                        ])
                        ->log('User logged out');
                } catch (\Exception $e) {
                    // Silently fail - don't block logout
                }
            })->afterResponse();
        }

        // Return simple redirect without Inertia
        return redirect('/login');
    }
}
