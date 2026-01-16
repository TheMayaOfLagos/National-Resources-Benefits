<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If user has 2FA enabled but hasn't verified in this session
        if ($user->two_factor_enabled && $user->two_factor_secret && !session('two_factor_verified')) {
            return redirect()->route('auth.two-factor.challenge');
        }

        return $next($request);
    }
}
