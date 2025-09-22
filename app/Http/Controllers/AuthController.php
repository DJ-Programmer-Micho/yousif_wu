<?php

namespace App\Http\Controllers;

use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function suspended() {
        return view('suspended.index');
    }

    public function login() {
        return view('pages.auth.index');
    }

    public function loginAction(Request $request) {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string|min:8',
            'g-recaptcha-response' => ['required', new ReCaptcha]
        ]);

        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('splash');
        }

        return back()->withErrors([
            'login' => 'Invalid email/username or password.',
        ])->withInput();
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}
