<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->status == 0) {
                Auth::logout();
                return redirect('/account-suspended')->with('alert', 'This is an alert message.');
            }
            return $next($request);
        } else {
            return redirect('/auth');
        }
    }
}
