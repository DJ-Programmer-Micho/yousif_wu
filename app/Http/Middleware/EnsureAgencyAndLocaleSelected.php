<?php

// app/Http/Middleware/EnsureAgencyAndLocaleSelected.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAgencyAndLocaleSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('agency') || !session()->has('locale')) {
            return redirect()->route('splash');
        }
        return $next($request);
    }
}
