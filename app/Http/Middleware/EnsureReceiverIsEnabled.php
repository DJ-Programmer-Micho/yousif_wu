<?php

// app/Http/Middleware/EnsureReceiverIsEnabled.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ReceiverGate;

class EnsureReceiverIsEnabled
{
    public function handle(Request $request, Closure $next)
    {
        // allow admins to always pass to view the page? Toggle this as you wish.
        $user = $request->user();
        $isAdmin = (int)($user->role ?? 0) === 1;

        if (!$isAdmin && ReceiverGate::isBlockedFor($user)) {
            abort(403, __('Receivers are disabled for your account.'));
        }
        return $next($request);
    }
}
