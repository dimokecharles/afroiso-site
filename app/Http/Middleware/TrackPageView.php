<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Counts visits to public pages for the admin dashboard, without cookies.
 *
 * Each visitor is stored as a code made from the IP address, browser and today's date. The code cannot be
 * turned back into an IP address and changes every day, so visits cannot be followed from one day to the next.
 */
class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($this->shouldCount($request, $response)) {
                $referrer = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

                PageView::create([
                    'path' => '/' . ltrim(substr($request->path(), 0, 190), '/'),
                    'referrer_host' => $referrer && $referrer !== $request->getHost() ? substr($referrer, 0, 190) : null,
                    'visitor' => hash('sha256', $request->ip() . '|' . $request->userAgent() . '|' . now()->toDateString() . '|' . config('app.key')),
                    'viewed_on' => now()->toDateString(),
                ]);
            }
        } catch (\Throwable $e) {
            // Counting visits must never break a page.
            Log::warning('Page view could not be recorded: ' . $e->getMessage());
        }

        return $response;
    }

    private function shouldCount(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200 || $request->user()) {
            return false;
        }

        if ($request->is('admin', 'admin/*', 'pay/*', 'up', 'sitemap.xml')) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        $agent = strtolower((string) $request->userAgent());

        return $agent !== '' && ! preg_match('/bot|crawl|spider|slurp|preview|monitor|curl|wget|python|headless/', $agent);
    }
}
