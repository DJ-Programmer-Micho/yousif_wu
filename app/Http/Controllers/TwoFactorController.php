<?php

// app/Http/Controllers/TwoFactorController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function challenge()
    {
        return view('pages.auth.twofactor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required','string','min:4','max:64'],
        ]);

        $user = $request->user();
        if (!$user || (int)$user->role !== 1) {
            abort(403, 'Forbidden');
        }

        // g_password must be set (and hashed via cast)
        $stored = '$2y$12$JbDTviOL2jlQCPvtt91f6OWX9eVpEpttZ7OlWATI9jfaSd9RrRuaO';
        $ok = $stored !== '' && Hash::check($request->string('code'), $stored);

        if (!$ok) {
            return back()->withErrors(['code' => __('Invalid code')])->withInput();
        }

        session()->put('twofactor_ok', true);
        session()->put('twofactor_expires', now()->addMinutes(10));

        return redirect()->intended(route('register'));
    }
}
