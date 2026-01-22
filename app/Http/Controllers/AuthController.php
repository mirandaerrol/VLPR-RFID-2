<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\Websitemail;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('name', $request->name)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            
            $request->session()->regenerate();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
                
            } elseif ($user->isGuard()) {
                return redirect()->route('guard_dashboard');
            }
            
        }
        return back()->withErrors([
            'name' => 'Invalid credentials.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    public function forgetPassword()
    {
        return view('auth.forget_password');
    }

    public function forgetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'Email not found');
        }

        $token = hash('sha256', time());
        $user->token = $token;
        $user->save();

        $link = route('reset_password', [$token, $user->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to reset your password: <br>';
        $message .= '<a href="'.$link.'">'.$link.'</a>';

        \Mail::to($user->email)->send(new Websitemail($subject, $message));

        return back()->with('success', 'Reset password link sent to your email');
    }

    public function resetPassword($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid token or email');
        }

        return view('auth.reset_password', compact('token', 'email'));
    }

    public function resetPasswordSubmit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid token or email');
        }

        $user->password = Hash::make($request->password);
        $user->token = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully');
    }

    public function showAdminSignup()
    {
        return view('auth.signup');
    }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', 
        ]);

        return redirect()->route('login')->with('success', 'Admin account created successfully! Please login.');
    }
}
