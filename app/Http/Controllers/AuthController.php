<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\Websitemail;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(Request $request)
    {
        return view("auth.login");
    }

    /**
     * Handle login with rate limiting and session security.
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        
        $user = User::where('name', $request->name)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            
            // Regenerate session ID to prevent session fixation attacks
            $request->session()->regenerate();
            
            Log::info('User logged in', ['user_id' => $user->id, 'role' => $user->role, 'ip' => $request->ip()]);
            
            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isGuard()) {
                return redirect()->route('guard_dashboard');
            } elseif ($user->isMaster()) {
                return redirect()->route('master.dashboard');
            }
        }
        
        Log::warning('Failed login attempt', ['name' => $request->name, 'ip' => $request->ip()]);
        
        return back()->withErrors([
            'name' => 'Invalid credentials.',
        ]);
    }

    /**
     * Handle logout with proper session invalidation.
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        Auth::logout();
        
        // Invalidate the session completely
        $request->session()->invalidate();
        
        // Regenerate the CSRF token
        $request->session()->regenerateToken();
        
        Log::info('User logged out', ['user_id' => $userId, 'ip' => $request->ip()]);
        
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    /**
     * Show the forgot password form.
     */
    public function forgetPassword()
    {
        return view('auth.forget_password');
    }

    /**
     * Handle forgot password submission with secure token generation.
     */
    public function forgetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // Return generic message to prevent email enumeration
            return back()->with('success', 'If an account with that email exists, a reset link has been sent.');
        }

        // Use cryptographically secure token
        $token = Str::random(64);
        $user->token = $token;
        $user->save();

        $link = route('reset_password', [$token, $user->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to reset your password: <br>';
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        try {
            \Mail::to($user->email)->send(new Websitemail($subject, $message));
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', ['email' => $user->email, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send reset email. Please try again later.');
        }

        return back()->with('success', 'If an account with that email exists, a reset link has been sent.');
    }

    /**
     * Show the reset password form.
     */
    public function resetPassword($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired reset link.');
        }

        return view('auth.reset_password', compact('token', 'email'));
    }

    /**
     * Handle password reset submission.
     */
    public function resetPasswordSubmit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired reset link.');
        }

        $user->password = Hash::make($request->password);
        $user->token = null;
        $user->save();

        Log::info('Password reset successful', ['user_id' => $user->id, 'ip' => $request->ip()]);

        return redirect()->route('login')->with('success', 'Password reset successfully');
    }

    /**
     * Show the admin signup form.
     */
    public function showAdminSignup()
    {
        return view('auth.signup');
    }

    /**
     * Create a new admin account with validation.
     */
    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', 
        ]);

        Log::info('New admin account created', ['name' => $request->name, 'ip' => $request->ip()]);

        return redirect()->route('login')->with('success', 'Admin account created successfully! Please login.');
    }
}
