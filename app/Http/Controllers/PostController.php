<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('categories');

        // Поиск по title, slug, content
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Фильтр по дате
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Сортировка
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate(15)->withQueryString();

        // Получаем общую статистику для всех постов (без фильтров)
        $totalPosts = Post::count();
        $draftPosts = Post::where('status', 'draft')->count();
        $publishedPosts = Post::where('status', 'published')->count();
        $archivedPosts = Post::where('status', 'archived')->count();

        return view('dashboard.posts', compact('posts', 'totalPosts', 'draftPosts', 'publishedPosts', 'archivedPosts'));
    }

    public function create()
    {
        $availableLocales = LaravelLocalization::getSupportedLocales();
        $defaultLocale = config('app.locale', 'en');

        // Сортируем языки: основной язык первым
        $localeKeys = array_keys($availableLocales);
        $sortedLocales = [];

        // Добавляем основной язык первым
        if (isset($availableLocales[$defaultLocale])) {
            $sortedLocales[$defaultLocale] = $availableLocales[$defaultLocale];
        }

        // Добавляем остальные языки
        foreach ($localeKeys as $key) {
            if ($key !== $defaultLocale && isset($availableLocales[$key])) {
                $sortedLocales[$key] = $availableLocales[$key];
            }
        }

        return view('dashboard.create-post', [
            'locales' => $sortedLocales,
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function store(Request $request)
    {
        $availableLocales = LaravelLocalization::getSupportedLocales();
        $localeKeys = array_keys($availableLocales);
        $defaultLocale = config('app.locale', 'en');

        $rules = [
            'slug' => 'required|string|max:255|unique:posts',
            'status' => 'required|in:draft,published,archived',
            'translations' => 'nullable|array',
        ];

        foreach ($localeKeys as $locale) {
            $rules["title_{$locale}"] = 'nullable|string|max:255';
            $rules["content_{$locale}"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Получаем данные основного языка
        $primaryLocale = null;
        foreach ($localeKeys as $locale) {
            $titleValue = trim((string) $request->input("title_{$locale}", ''));
            $contentValue = trim((string) $request->input("content_{$locale}", ''));
            if ($titleValue !== '' && $contentValue !== '') {
                $primaryLocale = $primaryLocale ?? $locale;
            }
        }

        if (! $primaryLocale) {
            return back()
                ->withErrors([
                    "title_{$defaultLocale}" => 'Please fill title and content for at least one language.',
                ])
                ->withInput();
        }

        $defaultTitle = trim((string) $request->input("title_{$defaultLocale}", ''));
        $defaultContent = trim((string) $request->input("content_{$defaultLocale}", ''));
        $baseLocale = ($defaultTitle !== '' && $defaultContent !== '') ? $defaultLocale : $primaryLocale;

        $title = $request->input("title_{$baseLocale}");
        $content = $request->input("content_{$baseLocale}");
        $thumbnail = $request->input("thumbnail_{$baseLocale}");
        $metaTitle = $request->input("meta_title_{$baseLocale}");
        $metaDescription = $request->input("meta_description_{$baseLocale}");
        $metaKeywords = $request->input("meta_keywords_{$baseLocale}");

        // Обработка переводов
        $translations = [];

        foreach ($localeKeys as $locale) {
            $translations[$locale] = [
                'title' => $request->input("title_{$locale}", ''),
                'content' => $request->input("content_{$locale}", ''),
                'meta_title' => $request->input("meta_title_{$locale}", ''),
                'meta_description' => $request->input("meta_description_{$locale}", ''),
                'meta_keywords' => $request->input("meta_keywords_{$locale}", ''),
                'thumbnail' => $request->input("thumbnail_{$locale}", ''),
            ];
        }

        // Если передан JSON редактор, используем его данные
        if ($request->filled('translations_json')) {
            try {
                $jsonTranslations = json_decode($request->input('translations_json'), true);
                if (is_array($jsonTranslations)) {
                    // Объединяем с существующими переводами
                    foreach ($jsonTranslations as $locale => $data) {
                        if (isset($translations[$locale]) && is_array($data)) {
                            $translations[$locale] = array_merge($translations[$locale], $data);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Игнорируем ошибки парсинга JSON
            }
        }

        // Создаем пост с валидированными данными
        $post = Post::create([
            'title' => $title,
            'content' => $content,
            'thumbnail' => $thumbnail ?: null,
            'slug' => $validated['slug'],
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'meta_keywords' => $metaKeywords ?: null,
            'status' => $validated['status'],
            'translations' => $translations,
        ]);

        return redirect()->route('dashboard.posts')
            ->with('success', 'Пост успешно создан!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $availableLocales = LaravelLocalization::getSupportedLocales();
        $defaultLocale = config('app.locale', 'en');

        // Сортируем языки: основной язык первым
        $localeKeys = array_keys($availableLocales);
        $sortedLocales = [];

        // Добавляем основной язык первым
        if (isset($availableLocales[$defaultLocale])) {
            $sortedLocales[$defaultLocale] = $availableLocales[$defaultLocale];
        }

        // Добавляем остальные языки
        foreach ($localeKeys as $key) {
            if ($key !== $defaultLocale && isset($availableLocales[$key])) {
                $sortedLocales[$key] = $availableLocales[$key];
            }
        }

        return view('dashboard.edit-post', [
            'post' => $post,
            'locales' => $sortedLocales,
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $availableLocales = LaravelLocalization::getSupportedLocales();
        $localeKeys = array_keys($availableLocales);
        $defaultLocale = config('app.locale', 'en');

        $rules = [
            'slug' => 'required|string|max:255|unique:posts,slug,'.$id,
            'status' => 'required|in:draft,published,archived',
            'translations' => 'nullable|array',
        ];

        foreach ($localeKeys as $locale) {
            $rules["title_{$locale}"] = 'nullable|string|max:255';
            $rules["content_{$locale}"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Получаем данные основного языка
        $primaryLocale = null;
        foreach ($localeKeys as $locale) {
            $titleValue = trim((string) $request->input("title_{$locale}", ''));
            $contentValue = trim((string) $request->input("content_{$locale}", ''));
            if ($titleValue !== '' && $contentValue !== '') {
                $primaryLocale = $primaryLocale ?? $locale;
            }
        }

        if (! $primaryLocale) {
            return back()
                ->withErrors([
                    "title_{$defaultLocale}" => 'Please fill title and content for at least one language.',
                ])
                ->withInput();
        }

        $defaultTitle = trim((string) $request->input("title_{$defaultLocale}", ''));
        $defaultContent = trim((string) $request->input("content_{$defaultLocale}", ''));
        $baseLocale = ($defaultTitle !== '' && $defaultContent !== '') ? $defaultLocale : $primaryLocale;

        $title = $request->input("title_{$baseLocale}");
        $content = $request->input("content_{$baseLocale}");
        $thumbnail = $request->input("thumbnail_{$baseLocale}");
        $metaTitle = $request->input("meta_title_{$baseLocale}");
        $metaDescription = $request->input("meta_description_{$baseLocale}");
        $metaKeywords = $request->input("meta_keywords_{$baseLocale}");

        // Обработка переводов
        $translations = [];

        foreach ($localeKeys as $locale) {
            $translations[$locale] = [
                'title' => $request->input("title_{$locale}", ''),
                'content' => $request->input("content_{$locale}", ''),
                'meta_title' => $request->input("meta_title_{$locale}", ''),
                'meta_description' => $request->input("meta_description_{$locale}", ''),
                'meta_keywords' => $request->input("meta_keywords_{$locale}", ''),
                'thumbnail' => $request->input("thumbnail_{$locale}", ''),
            ];
        }

        // Если передан JSON редактор, используем его данные
        if ($request->filled('translations_json')) {
            try {
                $jsonTranslations = json_decode($request->input('translations_json'), true);
                if (is_array($jsonTranslations)) {
                    // Объединяем с существующими переводами
                    foreach ($jsonTranslations as $locale => $data) {
                        if (isset($translations[$locale]) && is_array($data)) {
                            $translations[$locale] = array_merge($translations[$locale], $data);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Игнорируем ошибки парсинга JSON
            }
        }

        // Обновляем пост с валидированными данными
        $post->update([
            'title' => $title,
            'content' => $content,
            'thumbnail' => $thumbnail ?: null,
            'slug' => $validated['slug'],
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'meta_keywords' => $metaKeywords ?: null,
            'status' => $validated['status'],
            'translations' => $translations,
        ]);

        return redirect()->route('dashboard.posts')
            ->with('success', 'Пост успешно обновлен!');
    }
}
