<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BlogController extends Controller
{
    public function blog(): View
    {
        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $postsQuery = Post::where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        $currentLocale = app()->getLocale();
        $defaultLocale = LaravelLocalization::getDefaultLocale();

        $filteredPosts = $postsQuery
            ->filter(fn (Post $post) => $this->hasLocaleContent($post, $currentLocale, $defaultLocale))
            ->values();

        $pageItems = $filteredPosts
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values()
            ->map(fn (Post $post) => $this->applyLocaleTranslation($post, $currentLocale));

        $posts = new LengthAwarePaginator(
            $pageItems,
            $filteredPosts->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $basePath = '/blog';
        $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));
        $hreflangUrls = [];

        foreach ($supportedLocales as $localeCode) {
            if ($localeCode === $defaultLocale) {
                $hreflangUrls[$localeCode] = url($basePath);
                continue;
            }

            $hreflangUrls[$localeCode] = LaravelLocalization::getLocalizedURL($localeCode, $basePath);
        }

        $xDefaultUrl = url($basePath);
        $metaTitle = __('index.blog.title') . ' - ' . config('app.name', 'CheapVINReport');
        $metaDescription = __('index.blog.subtitle');

        return view('blog', compact('posts', 'hreflangUrls', 'xDefaultUrl', 'metaTitle', 'metaDescription'));
    }

    private function applyLocaleTranslation(Post $post, string $currentLocale): Post
    {
        $translations = $post->translations ?? [];
        $translation = $translations[$currentLocale] ?? null;

        if ($translation && is_array($translation)) {
            if (!empty($translation['title'])) {
                $post->title = $translation['title'];
            }

            if (!empty($translation['content'])) {
                $post->content = $translation['content'];
            }
        }

        return $post;
    }

    private function hasLocaleContent(Post $post, string $currentLocale, string $defaultLocale): bool
    {
        $translations = $post->translations ?? [];

        if ($currentLocale === $defaultLocale) {
            if (is_array($translations) && array_key_exists($defaultLocale, $translations)) {
                $translation = $translations[$defaultLocale] ?? null;
                if (!$translation || !is_array($translation)) {
                    return false;
                }

                return (
                    (isset($translation['title']) && trim($translation['title']) !== '') ||
                    (isset($translation['content']) && trim($translation['content']) !== '')
                );
            }

            return trim((string) $post->title) !== '' || trim((string) $post->content) !== '';
        }

        $translation = $translations[$currentLocale] ?? null;
        if (!$translation || !is_array($translation)) {
            return false;
        }

        return (
            (isset($translation['title']) && trim($translation['title']) !== '') ||
            (isset($translation['content']) && trim($translation['content']) !== '')
        );
    }

    // Post detail handled by HomeController@post for localized routes.
}
