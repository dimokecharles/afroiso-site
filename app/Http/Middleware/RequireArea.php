<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets a staff member into one area of the admin panel only if their role includes it.
 * Used on routes as "area:payments", "area:content" and so on. The areas are listed in App\Models\User.
 */
class RequireArea
{
    public function handle(Request $request, Closure $next, string $area): Response
    {
        $user = $request->user();

        // A login that has been switched off stops working straight away, even mid-session.
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'This login has been switched off. Ask an administrator.']);
        }

        // "self" is every signed-in staff member's own space: the dashboard redirect and their password page.
        abort_unless($area === 'self' || $user->canAccess($area), 403, 'Your role does not include this part of the admin panel.');

        return $next($request);
    }
}
