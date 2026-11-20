<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the UAT (test) copy of the site private.
 *
 * Does nothing unless UAT_MODE=true in .env, so the live site is unaffected. On UAT it asks for the
 * UAT_USER / UAT_PASSWORD login (when a password is set) and tells search engines not to index the site.
 */
class UatGate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('afroiso.uat')) {
            return $next($request);
        }

        $password = (string) config('afroiso.uat_password');

        if ($password !== '') {
            $userMatches = hash_equals((string) config('afroiso.uat_user'), (string) $request->getUser());
            $passwordMatches = hash_equals($password, (string) $request->getPassword());

            if (! $userMatches || ! $passwordMatches) {
                return response('This is a private test site. Please sign in.', 401, [
                    'WWW-Authenticate' => 'Basic realm="AfroISO UAT", charset="UTF-8"',
                    'X-Robots-Tag' => 'noindex, nofollow',
                ]);
            }
        }

        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
