<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives every guest visitor a stable, opaque token so guest chats and guest
 * deep research sessions can be scoped to the visitor that created them.
 *
 * The cookie is intentionally excluded from cookie encryption (see
 * EncryptCookies) so tests and first-party clients can present it as-is; the
 * value is a random bearer token and is only useful to its owner.
 */
class GuestIdentity
{
    public const COOKIE = 'magicai_guest_id';

    public const COOKIE_LIFETIME_MINUTES = 60 * 24 * 7;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        $token = $request->cookie(self::COOKIE);

        if (! is_string($token) || $token === '') {
            $token = Str::random(64);

            Cookie::queue(Cookie::make(
                self::COOKIE,
                $token,
                self::COOKIE_LIFETIME_MINUTES,
                path: '/',
                secure: $request->isSecure(),
                httpOnly: true,
                raw: false,
                sameSite: 'lax',
            ));
        }

        $request->cookies->set(self::COOKIE, $token);
        $request->attributes->set(self::COOKIE, $token);

        return $next($request);
    }

    public static function token(): ?string
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = request();
        $token = $request->attributes->get(self::COOKIE) ?? $request->cookie(self::COOKIE);

        return is_string($token) && $token !== '' ? $token : null;
    }
}
