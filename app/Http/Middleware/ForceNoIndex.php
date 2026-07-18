<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ForceNoIndex — staging safety middleware.
 *
 * RU (Yevhen): пока сайт живёт на staging-домене Railway, этот middleware
 * добавляет заголовок, который запрещает Google/боты индексировать копию.
 * На боевом сайте (APP_ENV=production) он НИЧЕГО не делает — прод не пострадает.
 *
 * EN (dev handoff): Adds an `X-Robots-Tag: noindex, nofollow` header to every
 * response on any non-production environment. This is a header-level signal
 * (stronger than a <meta> tag: it also covers non-HTML responses like PDFs,
 * sitemap.xml, JSON). It is a no-op when APP_ENV=production, so it is safe to
 * keep this middleware registered permanently when merging into the main repo.
 *
 * Registered globally in bootstrap/app.php via $middleware->append(...).
 */
class ForceNoIndex
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only block indexing when NOT in production.
        if (! app()->environment('production')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive', true);
        }

        return $response;
    }
}
