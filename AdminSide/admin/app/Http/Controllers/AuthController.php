<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Show registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email|max:100',
            'contact' => 'required|string|max:15',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact' => $request->contact,
            'password' => Hash::make($request->password)
        ]);

        // Redirect to login page instead of auto-login for extra security
        return redirect()->route('login')->with('success', 'Registration successful! Please login with your credentials.');
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Get the authenticated user
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->role === 'user') {
                // Regular users are redirected to UserSide
                return redirect('http://localhost:19000')->with('success', 'Login successful! Redirecting to user dashboard.');
            } else if (in_array($user->role, ['police', 'admin'])) {
                // Police and Admin users stay in AdminSide dashboard
                return redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
            }
            
            // Default redirect (fallback)
            return redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully!');
    }
}
