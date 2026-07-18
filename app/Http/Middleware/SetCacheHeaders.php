<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAge = 3600): Response
    {
        $response = $next($request);

        // Не кэшируем страницы для авторизованных пользователей
        if (auth()->check()) {
            return $response;
        }

        // Не кэшируем POST, PUT, DELETE запросы
        if (!in_array($request->method(), ['GET', 'HEAD'])) {
            return $response;
        }

        // Не кэшируем страницы с параметрами (кроме статических ресурсов)
        if ($request->hasAny(['id', 'key', 'session_id', 'order_id', 'orderReference']) && 
            !$this->isStaticResource($request)) {
            return $response;
        }

        // Не кэшируем админские и пользовательские страницы
        $excludedRoutes = [
            'dashboard',
            'panel',
            'profile',
            'settings',
            'users',
            'thank-you',
            'login',
            'register',
            'password',
            'verify-email',
            'confirm-password',
            'forgot-password',
        ];

        $path = $request->path();
        foreach ($excludedRoutes as $route) {
            if (str_contains($path, $route)) {
                return $response;
            }
        }

        // Устанавливаем заголовки кэширования для публичных страниц
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}, must-revalidate");
        $response->headers->set('Expires', now()->addSeconds($maxAge)->format('D, d M Y H:i:s \G\M\T'));
        
        // Добавляем ETag для валидации кэша
        if ($response->getContent()) {
            $etag = md5($response->getContent());
            $response->headers->set('ETag', '"' . $etag . '"');
            
            // Проверяем If-None-Match для 304 ответа
            if ($request->header('If-None-Match') === '"' . $etag . '"') {
                return response('', 304)->withHeaders([
                    'Cache-Control' => "public, max-age={$maxAge}, must-revalidate",
                    'Expires' => now()->addSeconds($maxAge)->format('D, d M Y H:i:s \G\M\T'),
                    'ETag' => '"' . $etag . '"',
                ]);
            }
        }

        return $response;
    }

    /**
     * Проверяет, является ли запрос статическим ресурсом
     */
    private function isStaticResource(Request $request): bool
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
        
        foreach ($staticExtensions as $ext) {
            if (str_ends_with($path, '.' . $ext)) {
                return true;
            }
        }
        
        return false;
    }
}
