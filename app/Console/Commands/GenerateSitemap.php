<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap 
                            {--output=public/sitemap.xml : Путь для сохранения sitemap.xml}
                            {--base-url= : Базовый URL сайта (по умолчанию из config)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует sitemap.xml на основе постов и страниц';

    /**
     * Поддерживаемые языки
     */
    protected array $supportedLanguages = [];
    protected string $defaultLocale;
    protected string $baseUrl = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputPath = $this->option('output');
        $baseUrl = $this->option('base-url') ?: Config::get('app.url', 'https://example.com');
        
        // Убираем слэш в конце URL
        $baseUrl = rtrim($baseUrl, '/');
        Config::set('app.url', $baseUrl);
        $this->baseUrl = $baseUrl;

        $this->supportedLanguages = array_keys(LaravelLocalization::getSupportedLocales());
        $this->defaultLocale = LaravelLocalization::getDefaultLocale();

        $this->info("Генерация sitemap.xml...");
        $this->info("Базовый URL: {$baseUrl}");
        $this->info("Выходной файл: {$outputPath}");

        // Получаем все опубликованные посты и страницы
        $posts = Post::where('status', 'published')->get();
        $pages = Page::where('status', 'published')->get();

        $this->info("Найдено постов: " . $posts->count());
        $this->info("Найдено страниц: " . $pages->count());

        // Генерируем XML
        $xml = $this->generateXml($baseUrl, $posts, $pages);

        // Сохраняем файл
        $directory = dirname($outputPath);
        if (!is_dir($directory) && $directory !== '.') {
            if (!mkdir($directory, 0755, true)) {
                $this->error("Не удалось создать директорию: {$directory}");
                return Command::FAILURE;
            }
        }

        if (file_put_contents($outputPath, $xml) === false) {
            $this->error("Не удалось сохранить файл: {$outputPath}");
            return Command::FAILURE;
        }

        $this->info("✓ Sitemap успешно создан: {$outputPath}");
        $this->info("Размер файла: " . $this->formatBytes(filesize($outputPath)));

        return Command::SUCCESS;
    }

    /**
     * Генерирует XML содержимое sitemap
     */
    protected function generateXml(string $baseUrl, $posts, $pages): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        // Главная страница
        $xml .= $this->generateUrlEntry($baseUrl, null, '1.0', 'daily', $this->getHomeTranslations());

        // Блог (листинг)
        $blogTranslations = $this->getBlogTranslations();
        $blogUrl = $this->getPrimaryUrl($blogTranslations);
        if ($blogUrl) {
            $xml .= $this->generateUrlEntry($blogUrl, null, '0.9', 'daily', $blogTranslations);
        }

        // Посты
        foreach ($posts as $post) {
            $translations = $this->getTranslations($post, 'post');
            $url = $this->getPrimaryUrl($translations);
            $lastmod = $post->updated_at ? $post->updated_at->format('Y-m-d') : null;
            
            if ($url) {
                $xml .= $this->generateUrlEntry($url, $lastmod, '0.8', 'weekly', $translations);
            }
        }

        // Страницы
        foreach ($pages as $page) {
            $translations = $this->getTranslations($page, 'page');
            $url = $this->getPrimaryUrl($translations);
            $lastmod = $page->updated_at ? $page->updated_at->format('Y-m-d') : null;
            
            if ($url) {
                $xml .= $this->generateUrlEntry($url, $lastmod, '0.7', 'monthly', $translations);
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Генерирует запись URL для sitemap
     */
    protected function generateUrlEntry(
        string $url,
        ?string $lastmod = null,
        string $priority = '0.5',
        string $changefreq = 'monthly',
        array $translations = []
    ): string {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n";
        
        if ($lastmod) {
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        }
        
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";

        // Добавляем альтернативные языковые версии (hreflang)
        if (!empty($translations)) {
            foreach ($translations as $lang => $translationUrl) {
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$lang}\" href=\"" . 
                        htmlspecialchars($translationUrl, ENT_XML1, 'UTF-8') . "\" />\n";
            }

            if (isset($translations[$this->defaultLocale])) {
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\""
                    . htmlspecialchars($translations[$this->defaultLocale], ENT_XML1, 'UTF-8') . "\" />\n";
            }
        }

        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Строит URL для поста
     */
    protected function getPrimaryUrl(array $translations): ?string
    {
        if (isset($translations[$this->defaultLocale])) {
            return $translations[$this->defaultLocale];
        }

        return $translations ? reset($translations) : null;
    }

    /**
     * Возвращает языковые URL для главной страницы
     */
    protected function getHomeTranslations(): array
    {
        $translations = [];

        foreach ($this->supportedLanguages as $lang) {
            if ($lang === $this->defaultLocale) {
                $translations[$lang] = $this->baseUrl;
                continue;
            }

            $translations[$lang] = LaravelLocalization::getLocalizedURL($lang, '/');
        }

        return $translations;
    }

    /**
     * Возвращает языковые URL для блога
     */
    protected function getBlogTranslations(): array
    {
        $translations = [];
        $path = '/blog';

        foreach ($this->supportedLanguages as $lang) {
            if ($lang === $this->defaultLocale) {
                $translations[$lang] = $this->baseUrl . $path;
                continue;
            }

            $translations[$lang] = LaravelLocalization::getLocalizedURL($lang, $path);
        }

        return $translations;
    }

    /**
     * Получает переводы для поста/страницы и строит URL для каждого языка
     */
    protected function getTranslations($model, string $type): array
    {
        $translations = [];
        $mainTranslations = $model->translations ?? [];

        foreach ($this->supportedLanguages as $lang) {
            if (!$this->hasLocaleContent($model, $lang, $mainTranslations)) {
                continue;
            }

            $path = $type === 'post'
                ? '/blog/' . ltrim($model->slug, '/')
                : '/page/' . ltrim($model->slug, '/');

            if ($lang === $this->defaultLocale) {
                $translations[$lang] = $this->baseUrl . $path;
                continue;
            }

            $translations[$lang] = LaravelLocalization::getLocalizedURL($lang, $path);
        }

        return $translations;
    }

    protected function hasLocaleContent($model, string $locale, array $mainTranslations): bool
    {
        if ($locale === $this->defaultLocale) {
            if (array_key_exists($locale, $mainTranslations)) {
                $translation = $mainTranslations[$locale] ?? null;
                if (! $translation || ! is_array($translation)) {
                    return false;
                }

                return (
                    (isset($translation['title']) && trim($translation['title']) !== '') ||
                    (isset($translation['content']) && trim($translation['content']) !== '')
                );
            }

            return trim((string) $model->title) !== '' || trim((string) $model->content) !== '';
        }

        $translation = $mainTranslations[$locale] ?? null;
        if (! $translation || ! is_array($translation)) {
            return false;
        }

        return (
            (isset($translation['title']) && trim($translation['title']) !== '') ||
            (isset($translation['content']) && trim($translation['content']) !== '')
        );
    }

    /**
     * Форматирует размер файла в читаемый вид
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
