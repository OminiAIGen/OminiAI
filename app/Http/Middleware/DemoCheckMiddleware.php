<?php

namespace App\Http\Middleware;

use App\Helpers\Classes\Helper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Helper::appIsDemo()) {
            $message = trans('This feature is disabled in demo mode.');

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $message,
                ], 403);
            }

            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => $message,
            ]);
        }

        return $next($request);
    }
}
