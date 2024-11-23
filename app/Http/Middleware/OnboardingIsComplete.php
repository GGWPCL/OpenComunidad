<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardingIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->onboarding && !$request->routeIs('onboarding.*')) {
            return redirect()->route('onboarding.index');
        }

        return $next($request);
    }
}
