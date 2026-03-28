<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('corporate');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'SuperAdmin' || $user->role === 'Admin') {
                if ($user->hasPermission('manage_users')) {
                    return redirect()->route('admin.users');
                }

                if ($user->hasPermission('access_admin_dashboard')) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('townhall');
            }

            return redirect()->route('townhall');

        }

        return back()->withErrors([
            'email' => 'Invalid credentials.'
        ]);

    }

    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');

    }

}
