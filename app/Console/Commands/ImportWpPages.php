<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportWpPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-wp-pages
                            {file=wp_pages_clean_for_laravel_2.jsonl : Путь к JSONL файлу со страницами}
                            {--skip-existing : Пропускать страницы с существующими slug}
                            {--dry-run : Тестовый запуск без вставки в БД}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортирует страницы из WordPress JSONL файла в Laravel базу данных с поддержкой мультиязычности';

    /**
     * Поддерживаемые языки
     */
    protected array $supportedLanguages = ['en', 'ru', 'uk', 'es'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $skipExisting = $this->option('skip-existing');
        $dryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("Файл не найден: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Начинаем импорт страниц из: {$filePath}");

        if ($dryRun) {
            $this->warn("РЕЖИМ ТЕСТОВОГО ЗАПУСКА - данные не будут сохранены в БД");
        }

        // Парсим JSONL файл
        $this->info("\n1. Парсинг JSONL файла...");
        $pagesByDate = $this->parseJsonlFile($filePath);

        if (empty($pagesByDate)) {
            $this->error("Не найдено страниц для импорта");
            return Command::FAILURE;
        }

        $this->info("Найдено уникальных страниц (по post_date): " . count($pagesByDate));
        $totalTranslations = array_sum(array_map('count', $pagesByDate));
        $this->info("Всего переводов: {$totalTranslations}");

        // Импортируем страницы
        $this->info("\n2. Импорт страниц в базу данных...");

        $stats = [
            'inserted' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $bar = $this->output->createProgressBar(count($pagesByDate));
        $bar->start();

        foreach ($pagesByDate as $postDate => $pages) {
            try {
                $result = $this->importPage($pages, $skipExisting, $dryRun);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->newLine();
                $this->error("Ошибка при импорте страницы post_date={$postDate}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Выводим статистику
        $this->info("=== Результаты импорта ===");
        $this->info("Вставлено: {$stats['inserted']}");
        $this->info("Пропущено: {$stats['skipped']}");
        $this->info("Ошибок: {$stats['errors']}");

        if ($dryRun) {
            $this->warn("\nЭто был тестовый запуск. Для реального импорта запустите команду без флага --dry-run");
        }

        return Command::SUCCESS;
    }

    /**
     * Парсит JSONL файл и группирует страницы по post_date
     * (так как у одной страницы на разных языках могут быть разные wp_id)
     */
    protected function parseJsonlFile(string $filePath): array
    {
        $pagesByDate = [];
        $lineNumber = 0;

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Не удалось открыть файл: {$filePath}");
        }

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            try {
                $pageData = json_decode($line, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->warn("Ошибка парсинга JSON на строке {$lineNumber}: " . json_last_error_msg());
                    continue;
                }

                // Проверяем, что это страница
                $postType = $pageData['post_type'] ?? null;
                if ($postType !== 'page') {
                    continue;
                }

                $postDate = $pageData['post_date'] ?? null;
                if (!$postDate) {
                    continue;
                }

                $status = $pageData['status'] ?? 'publish';
                if (!in_array($status, ['publish', 'draft'])) {
                    continue;
                }

                $pageInfo = [
                    'wp_id' => $pageData['wp_id'] ?? null,
                    'language' => $pageData['language'] ?? 'en',
                    'slug' => $pageData['slug'] ?? '',
                    'title' => $pageData['title'] ?? '',
                    'content' => $pageData['content_html'] ?? '',
                    'status' => $status === 'publish' ? 'published' : 'draft',
                    'post_date' => $postDate,
                ];

                // Группируем по post_date (ключ для группировки переводов)
                if (!isset($pagesByDate[$postDate])) {
                    $pagesByDate[$postDate] = [];
                }

                $pagesByDate[$postDate][] = $pageInfo;

            } catch (\Exception $e) {
                $this->warn("Ошибка обработки строки {$lineNumber}: " . $e->getMessage());
                continue;
            }
        }

        fclose($handle);
        return $pagesByDate;
    }

    /**
     * Импортирует одну страницу (со всеми переводами)
     */
    protected function importPage(array $pages, bool $skipExisting, bool $dryRun): string
    {
        if (empty($pages)) {
            return 'skipped';
        }

        // Строим translations JSON
        $translations = $this->buildTranslationsJson($pages);

        if (empty($translations)) {
            return 'skipped';
        }

        // Выбираем основную страницу
        $mainPage = $this->getMainPage($pages);
        if (!$mainPage) {
            return 'skipped';
        }

        // Нормализуем slug
        $slug = $this->normalizeSlug($mainPage['slug']);
        if (!$slug) {
            $slug = $this->generateSlugFromTitle($mainPage['title']);
        }

        // Проверяем существование
        if ($skipExisting && Page::where('slug', $slug)->exists()) {
            return 'skipped';
        }

        // Парсим дату для created_at
        $createdAt = $this->parsePostDate($mainPage['post_date']);

        if ($dryRun) {
            $langs = implode(', ', array_keys($translations));
            $this->newLine();
            $this->line("  [DRY-RUN] Будет вставлена: {$mainPage['title']} (slug: {$slug}, языков: " . count($translations) . " [{$langs}], дата: {$createdAt})");
            return 'inserted';
        }

        // Вставляем в БД
        try {
            DB::beginTransaction();

            Page::create([
                'title' => $mainPage['title'],
                'slug' => $slug,
                'content' => $mainPage['content'],
                'meta_title' => $mainPage['title'], // По умолчанию = title
                'meta_description' => null,
                'meta_keywords' => null,
                'status' => $mainPage['status'],
                'translations' => $translations,
                'created_at' => $createdAt,
                'updated_at' => $createdAt, // Обновляем тоже для консистентности
            ]);

            DB::commit();

            return 'inserted';
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Строит JSON для поля translations
     */
    protected function buildTranslationsJson(array $pages): array
    {
        $translations = [];

        foreach ($pages as $page) {
            $lang = $page['language'];

            if (!in_array($lang, $this->supportedLanguages)) {
                continue;
            }

            $translations[$lang] = [
                'title' => $page['title'],
                'content' => $page['content'],
                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
            ];
        }

        return $translations;
    }

    /**
     * Выбирает основную страницу (приоритет: en > ru > первый)
     */
    protected function getMainPage(array $pages): ?array
    {
        $priorityLanguages = ['en', 'ru', 'uk', 'es'];

        foreach ($priorityLanguages as $lang) {
            foreach ($pages as $page) {
                if ($page['language'] === $lang) {
                    return $page;
                }
            }
        }

        return $pages[0] ?? null;
    }

    /**
     * Нормализует slug (убирает языковые префиксы)
     */
    protected function normalizeSlug(?string $slug): ?string
    {
        if (empty($slug)) {
            return null;
        }

        $parts = explode('/', trim($slug, '/'));

        if (!empty($parts) && in_array($parts[0], $this->supportedLanguages)) {
            array_shift($parts);
        }

        $normalized = implode('/', $parts);
        return !empty($normalized) ? $normalized : null;
    }

    /**
     * Генерирует slug из заголовка
     */
    protected function generateSlugFromTitle(string $title): string
    {
        $slug = Str::slug($title);
        return Str::limit($slug, 200, '');
    }

    /**
     * Парсит post_date в Carbon объект для created_at
     * Очень важно: created_at должен совпадать с post_date из файла
     */
    protected function parsePostDate(?string $postDate): Carbon
    {
        if (empty($postDate)) {
            return Carbon::now();
        }

        try {
            // Пробуем стандартный формат WordPress: Y-m-d H:i:s
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $postDate);
            if ($date !== false) {
                return $date;
            }
        } catch (\Exception $e) {
            // Если не получилось, пробуем другие форматы
        }

        try {
            // Пробуем автоматический парсинг
            $date = Carbon::parse($postDate);
            return $date;
        } catch (\Exception $e) {
            $this->warn("Не удалось распарсить дату: {$postDate}, используется текущая дата");
            return Carbon::now();
        }
    }
}
