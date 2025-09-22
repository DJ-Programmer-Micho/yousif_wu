<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class Localization
{
    protected array $supported = ['en','ar','ku'];

    public function handle(Request $request, Closure $next): Response
    {
        // Priority: ?locale=xx > session('locale') > config default
        $locale = $request->query('locale', $request->session()->get('locale', config('app.locale')));

        // Guard against unsupported values
        if (! in_array($locale, $this->supported, true)) {
            $locale = config('app.locale');
        }

        // Apply for this request
        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    // If you're posting to this method from the language switcher
    public function setLocale(Request $request)
    {
        $validated = $request->validate([
            'locale'   => 'required|in:en,ar,ku',
            'redirect' => 'nullable|string',
        ]);

        $request->session()->put('locale', $validated['locale']); // single source of truth
        App::setLocale($validated['locale']);

        return redirect()->to($validated['redirect'] ?? url()->previous() ?? route('dashboard'));
    }
}
