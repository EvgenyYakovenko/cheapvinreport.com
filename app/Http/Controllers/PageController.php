<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PageController extends Controller
{
    // Admin methods
    public function index(Request $request)
    {
        $query = Page::query();

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

        $pages = $query->paginate(15)->withQueryString();

        // Получаем общую статистику для всех страниц (без фильтров)
        $totalPages = Page::count();
        $draftPages = Page::where('status', 'draft')->count();
        $publishedPages = Page::where('status', 'published')->count();
        $archivedPages = Page::where('status', 'archived')->count();

        return view('dashboard.pages', compact('pages', 'totalPages', 'draftPages', 'publishedPages', 'archivedPages'));
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

        return view('dashboard.create-page', [
            'locales' => $sortedLocales,
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function store(Request $request)
    {
        $defaultLocale = config('app.locale', 'en');

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:pages',
            'status' => 'required|in:draft,published,archived',
            // Основной язык - обязательные поля
            "title_{$defaultLocale}" => 'required|string|max:255',
            "content_{$defaultLocale}" => 'required|string',
            // Остальные языки - необязательные
            'translations' => 'nullable|array',
        ]);

        // Получаем данные основного языка
        $title = $request->input("title_{$defaultLocale}");
        $content = $request->input("content_{$defaultLocale}");
        // Декодируем HTML-сущности, если они были экранированы
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $metaTitle = $request->input("meta_title_{$defaultLocale}");
        $metaDescription = $request->input("meta_description_{$defaultLocale}");
        $metaKeywords = $request->input("meta_keywords_{$defaultLocale}");

        // Обработка переводов
        $availableLocales = LaravelLocalization::getSupportedLocales();
        $localeKeys = array_keys($availableLocales);
        $translations = [];

        foreach ($localeKeys as $locale) {
            $localeContent = $request->input("content_{$locale}", '');
            // Декодируем HTML-сущности, если они были экранированы
            $localeContent = html_entity_decode($localeContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $translations[$locale] = [
                'title' => $request->input("title_{$locale}", ''),
                'content' => $localeContent,
                'meta_title' => $request->input("meta_title_{$locale}", ''),
                'meta_description' => $request->input("meta_description_{$locale}", ''),
                'meta_keywords' => $request->input("meta_keywords_{$locale}", ''),
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

        // Создаем страницу с валидированными данными
        $page = Page::create([
            'title' => $title,
            'content' => $content,
            'slug' => $validated['slug'],
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'meta_keywords' => $metaKeywords ?: null,
            'status' => $validated['status'],
            'translations' => $translations,
        ]);

        return redirect()->route('dashboard.pages')
            ->with('success', 'Страница успешно создана!');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
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

        return view('dashboard.edit-page', [
            'page' => $page,
            'locales' => $sortedLocales,
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $defaultLocale = config('app.locale', 'en');

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:pages,slug,'.$id,
            'status' => 'required|in:draft,published,archived',
            // Основной язык - обязательные поля
            "title_{$defaultLocale}" => 'required|string|max:255',
            "content_{$defaultLocale}" => 'required|string',
            // Остальные языки - необязательные
            'translations' => 'nullable|array',
        ]);

        // Получаем данные основного языка
        $title = $request->input("title_{$defaultLocale}");
        $content = $request->input("content_{$defaultLocale}");
        // Декодируем HTML-сущности, если они были экранированы
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $metaTitle = $request->input("meta_title_{$defaultLocale}");
        $metaDescription = $request->input("meta_description_{$defaultLocale}");
        $metaKeywords = $request->input("meta_keywords_{$defaultLocale}");

        // Обработка переводов
        $availableLocales = LaravelLocalization::getSupportedLocales();
        $localeKeys = array_keys($availableLocales);
        $translations = [];

        foreach ($localeKeys as $locale) {
            $localeContent = $request->input("content_{$locale}", '');
            // Декодируем HTML-сущности, если они были экранированы
            $localeContent = html_entity_decode($localeContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $translations[$locale] = [
                'title' => $request->input("title_{$locale}", ''),
                'content' => $localeContent,
                'meta_title' => $request->input("meta_title_{$locale}", ''),
                'meta_description' => $request->input("meta_description_{$locale}", ''),
                'meta_keywords' => $request->input("meta_keywords_{$locale}", ''),
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

        // Обновляем страницу с валидированными данными
        $page->update([
            'title' => $title,
            'content' => $content,
            'slug' => $validated['slug'],
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'meta_keywords' => $metaKeywords ?: null,
            'status' => $validated['status'],
            'translations' => $translations,
        ]);

        return redirect()->route('dashboard.pages')
            ->with('success', 'Страница успешно обновлена!');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('dashboard.pages')
            ->with('success', 'Страница успешно удалена!');
    }
}
