<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportWpPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-wp-posts
                            {file=wp_posts_clean_for_laravel.jsonl : Путь к JSONL файлу с постами}
                            {--skip-existing : Пропускать посты с существующими slug}
                            {--dry-run : Тестовый запуск без вставки в БД}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортирует посты из WordPress JSONL файла в Laravel базу данных с поддержкой мультиязычности';

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

        $this->info("Начинаем импорт постов из: {$filePath}");

        if ($dryRun) {
            $this->warn("РЕЖИМ ТЕСТОВОГО ЗАПУСКА - данные не будут сохранены в БД");
        }

        // Парсим JSONL файл
        $this->info("\n1. Парсинг JSONL файла...");
        $postsByDate = $this->parseJsonlFile($filePath);

        if (empty($postsByDate)) {
            $this->error("Не найдено постов для импорта");
            return Command::FAILURE;
        }

        $this->info("Найдено уникальных постов (по post_date): " . count($postsByDate));
        $totalTranslations = array_sum(array_map('count', $postsByDate));
        $this->info("Всего переводов: {$totalTranslations}");

        // Импортируем посты
        $this->info("\n2. Импорт постов в базу данных...");

        $stats = [
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $bar = $this->output->createProgressBar(count($postsByDate));
        $bar->start();

        foreach ($postsByDate as $postDate => $posts) {
            try {
                $result = $this->importPost($posts, $skipExisting, $dryRun);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->newLine();
                $this->error("Ошибка при импорте поста post_date={$postDate}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Выводим статистику
        $this->info("=== Результаты импорта ===");
        $this->info("Создано новых: {$stats['inserted']}");
        $this->info("Обновлено: {$stats['updated']}");
        $this->info("Пропущено: {$stats['skipped']}");
        $this->info("Ошибок: {$stats['errors']}");

        if ($dryRun) {
            $this->warn("\nЭто был тестовый запуск. Для реального импорта запустите команду без флага --dry-run");
        }

        return Command::SUCCESS;
    }

    /**
     * Парсит JSONL файл и группирует посты по post_date
     * (так как у одного поста на разных языках могут быть разные wp_id)
     */
    protected function parseJsonlFile(string $filePath): array
    {
        $postsByDate = [];
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
                $postData = json_decode($line, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->warn("Ошибка парсинга JSON на строке {$lineNumber}: " . json_last_error_msg());
                    continue;
                }

                $postDate = $postData['post_date'] ?? null;
                if (!$postDate) {
                    continue;
                }

                $status = $postData['status'] ?? 'publish';
                if (!in_array($status, ['publish', 'draft'])) {
                    continue;
                }

                $postInfo = [
                    'wp_id' => $postData['wp_id'] ?? null,
                    'language' => $postData['language'] ?? 'en',
                    'slug' => $postData['slug'] ?? '',
                    'title' => $postData['title'] ?? '',
                    'content' => $postData['content_html'] ?? '',
                    'thumbnail' => $this->extractThumbnailFromContent($postData['content_html'] ?? ''),
                    'status' => $status === 'publish' ? 'published' : 'draft',
                    'post_date' => $postDate,
                ];

                // Группируем по post_date (ключ для группировки переводов)
                if (!isset($postsByDate[$postDate])) {
                    $postsByDate[$postDate] = [];
                }

                $postsByDate[$postDate][] = $postInfo;

            } catch (\Exception $e) {
                $this->warn("Ошибка обработки строки {$lineNumber}: " . $e->getMessage());
                continue;
            }
        }

        fclose($handle);
        return $postsByDate;
    }

    /**
     * Импортирует один пост (со всеми переводами)
     * Возвращает: 'inserted', 'updated', 'skipped'
     */
    protected function importPost(array $posts, bool $skipExisting, bool $dryRun): string
    {
        if (empty($posts)) {
            return 'skipped';
        }

        // Строим translations JSON
        $translations = $this->buildTranslationsJson($posts);

        if (empty($translations)) {
            return 'skipped';
        }

        // Выбираем основной пост
        $mainPost = $this->getMainPost($posts);
        if (!$mainPost) {
            return 'skipped';
        }

        // Нормализуем slug
        $slug = $this->normalizeSlug($mainPost['slug']);
        if (!$slug) {
            $slug = $this->generateSlugFromTitle($mainPost['title']);
        }

        // Парсим дату для created_at (очень важно: created_at должен совпадать с post_date)
        $createdAt = $this->parsePostDate($mainPost['post_date']);

        // Проверяем существование поста
        $existingPost = Post::where('slug', $slug)->first();
        $isUpdate = $existingPost !== null;

        if ($skipExisting && $isUpdate) {
            return 'skipped';
        }

        if ($dryRun) {
            $langs = implode(', ', array_keys($translations));
            $action = $isUpdate ? 'обновлен' : 'вставлен';
            $this->newLine();
            $this->line("  [DRY-RUN] Будет {$action}: {$mainPost['title']} (slug: {$slug}, языков: " . count($translations) . " [{$langs}], дата: {$createdAt})");
            return $isUpdate ? 'updated' : 'inserted';
        }

        // Обновляем или создаем пост
        try {
            DB::beginTransaction();

            $baseData = [
                'title' => $mainPost['title'],
                'content' => $mainPost['content'],
                'slug' => $slug,
                'thumbnail' => $mainPost['thumbnail'],
                'meta_title' => $mainPost['title'], // По умолчанию = title
                'meta_description' => null,
                'meta_keywords' => null,
                'status' => $mainPost['status'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if ($isUpdate) {
                // Обновляем существующий пост (включая created_at из post_date)
                // Используем DB::table для гарантированного обновления всех полей включая timestamps
                DB::table('posts')
                    ->where('id', $existingPost->id)
                    ->update(array_merge($baseData, [
                        'translations' => json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]));

                DB::commit();
                return 'updated';
            } else {
                // Создаем новый пост (Eloquent автоматически сериализует JSON)
                Post::create(array_merge($baseData, [
                    'translations' => $translations,
                ]));

                DB::commit();
                return 'inserted';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Строит JSON для поля translations
     */
    protected function buildTranslationsJson(array $posts): array
    {
        $translations = [];

        foreach ($posts as $post) {
            $lang = $post['language'];

            if (!in_array($lang, $this->supportedLanguages)) {
                continue;
            }

            $translations[$lang] = [
                'title' => $post['title'],
                'content' => $post['content'],
                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'thumbnail' => $post['thumbnail'],
            ];
        }

        return $translations;
    }

    /**
     * Выбирает основной пост (приоритет: en > ru > первый)
     */
    protected function getMainPost(array $posts): ?array
    {
        $priorityLanguages = ['en', 'ru', 'uk', 'es'];

        foreach ($priorityLanguages as $lang) {
            foreach ($posts as $post) {
                if ($post['language'] === $lang) {
                    return $post;
                }
            }
        }

        return $posts[0] ?? null;
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
     * Извлекает URL первого изображения из контента
     */
    protected function extractThumbnailFromContent(?string $content): ?string
    {
        if (empty($content)) {
            return null;
        }

        // Ищем img теги
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
            return $matches[1];
        }

        // Ищем прямые ссылки на изображения
        if (preg_match('/https?:\/\/[^\s<>"]+\.(jpg|jpeg|png|gif|webp)/i', $content, $matches)) {
            return $matches[0];
        }

        return null;
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
