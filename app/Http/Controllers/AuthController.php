<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        sleep(1);
        // Validate
        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        // Register
        $user = User::create($fields);

        // Login
        Auth::login($user);

        // Redirect
        return redirect()->route('home');
    }

    public function login(Request $request){
       $fields = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required']
       ]);

       if(Auth::attempt($fields, $request->remember)){
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
               'email' => $fields['email'],
               'ip_address' => $request->ip(),
               'user_agent' => $request->userAgent(),
               'attempt_time' => now()
           ])
           ->log('Failed login attempt');

       return back()->withErrors([
        'email' => 'The provided credentials do not match our records.'
       ])->onlyInput('email');
    }

    public function logout(Request $request){
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

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
