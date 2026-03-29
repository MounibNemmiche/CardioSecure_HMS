<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMfaIsSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Skip if on MFA setup route or Fortify 2FA routes
        if ($request->routeIs('two-factor.*') ||
            $request->routeIs('mfa.setup') ||
            $request->routeIs('logout')) {
            return $next($request);
        }

        // If MFA has not been confirmed yet, redirect to setup
        if (is_null($user->two_factor_confirmed_at)) {
            return redirect()->route('mfa.setup');
        }

        return $next($request);
    }
}
