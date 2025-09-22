<?php

// app/Http/Middleware/AdminTwoFactor.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Only admins may pass; others are forbidden on admin routes.
        if (!$user || (int)$user->role !== 1) {
            abort(403, 'Forbidden');
        }

        // Allow the challenge page itself
        if ($request->routeIs('2fa.challenge') || $request->routeIs('2fa.verify')) {
            return $next($request);
        }

        // Check session flag (valid for 10 minutes)
        $ok     = session('twofactor_ok');
        $expiry = session('twofactor_expires');

        if ($ok && $expiry && Carbon::parse($expiry)->isFuture()) {
            return $next($request);
        }

        // Remember where we wanted to go, then ask for 2FA
        session()->put('url.intended', $request->fullUrl());
        return redirect()->route('2fa.challenge');
    }
}
