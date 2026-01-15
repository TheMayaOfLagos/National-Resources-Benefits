<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Routes that should be accessible even in maintenance mode
     */
    protected array $except = [
        'login',
        'logout',
        'admin/*',
        'filament/*',
        'livewire/*',
        'sanctum/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceMode = Setting::get('maintenance_mode', false);

        if ($maintenanceMode) {
            // Allow access to excluded routes
            if ($this->shouldPassThrough($request)) {
                return $next($request);
            }

            // Allow admin users to access the site
            $user = $request->user();
            if ($user && $user->hasRole(['super_admin', 'admin'])) {
                return $next($request);
            }

            // Return maintenance page for everyone else
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Site is currently under maintenance. Please check back later.',
                ], 503);
            }

            return Inertia::render('Maintenance', [
                'message' => Setting::get('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.'),
            ])->toResponse($request)->setStatusCode(503);
        }

        return $next($request);
    }

    /**
     * Determine if the request should pass through maintenance mode
     */
    protected function shouldPassThrough(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}