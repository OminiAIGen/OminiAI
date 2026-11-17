<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marker middleware for routes that must remain available to guests.
 *
 * The global route hardening in RouteServiceProvider forces authentication on
 * every dashboard route. Routes carrying this marker are explicit, reviewed
 * exceptions and keep their own authorization rules.
 */
class GuestAccessible
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
