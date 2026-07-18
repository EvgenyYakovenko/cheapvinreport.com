<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportWpUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-wp-users 
                            {file=users_email_name_balance.csv : Путь к CSV файлу с пользователями}
                            {--skip-existing : Пропускать существующих пользователей}
                            {--dry-run : Тестовый запуск без вставки в БД}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортирует пользователей из CSV файла в Laravel базу данных';

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

        $this->info("Начинаем импорт пользователей из: {$filePath}");
        
        if ($dryRun) {
            $this->warn("РЕЖИМ ТЕСТОВОГО ЗАПУСКА - данные не будут сохранены в БД");
        }

        // Парсим CSV файл
        $this->info("\n1. Парсинг CSV файла...");
        $users = $this->parseCsvFile($filePath);
        
        if (empty($users)) {
            $this->error("Не найдено пользователей для импорта");
            return Command::FAILURE;
        }

        $this->info("Найдено пользователей: " . count($users));

        // Импортируем пользователей
        $this->info("\n2. Импорт пользователей в базу данных...");
        
        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $userData) {
            try {
                $result = $this->importUser($userData, $skipExisting, $dryRun);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->newLine();
                $this->error("Ошибка при импорте пользователя {$userData['email']}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Выводим статистику
        $this->info("=== Результаты импорта ===");
        $this->info("Создано новых: {$stats['created']}");
        $this->info("Обновлено: {$stats['updated']}");
        $this->info("Пропущено: {$stats['skipped']}");
        $this->info("Ошибок: {$stats['errors']}");

        if ($dryRun) {
            $this->warn("\nЭто был тестовый запуск. Для реального импорта запустите команду без флага --dry-run");
        }

        return Command::SUCCESS;
    }

    /**
     * Парсит CSV файл и возвращает массив пользователей
     */
    protected function parseCsvFile(string $filePath): array
    {
        $users = [];
        $lineNumber = 0;

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Не удалось открыть файл: {$filePath}");
        }

        // Пропускаем заголовок
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;
            
            if (empty($row) || count($row) < 1) {
                continue;
            }

            try {
                // Парсим данные из CSV
                // Формат: User Email, Username, Available Balance
                $email = trim($row[0] ?? '');
                $username = trim($row[1] ?? '');
                $balance = trim($row[2] ?? '');

                if (empty($email)) {
                    continue;
                }

                // Валидация email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->warn("Строка {$lineNumber}: некорректный email '{$email}', пропускаем");
                    continue;
                }

                // Парсим баланс
                $availableBalance = $this->parseBalance($balance);
                
                // Вычисляем report_balance: делим на 3 и округляем в большую сторону
                $reportBalance = $this->calculateReportBalance($availableBalance);

                // Определяем name: если есть username, используем его, иначе часть email
                $name = !empty($username) ? $username : $this->generateNameFromEmail($email);

                $users[] = [
                    'email' => strtolower($email),
                    'name' => $name,
                    'balance' => 0, // Всегда 0
                    'report_balance' => $reportBalance,
                ];

            } catch (\Exception $e) {
                $this->warn("Ошибка обработки строки {$lineNumber}: " . $e->getMessage());
                continue;
            }
        }

        fclose($handle);
        return $users;
    }

    /**
     * Парсит баланс из строки
     */
    protected function parseBalance(?string $balance): float
    {
        if (empty($balance) || trim($balance) === '') {
            return 0.0;
        }

        // Убираем пробелы и пробуем преобразовать в число
        $balance = trim($balance);
        $balance = str_replace(',', '.', $balance); // Заменяем запятую на точку
        
        $floatBalance = (float) $balance;
        
        return max(0.0, $floatBalance); // Не может быть отрицательным
    }

    /**
     * Вычисляет report_balance: делим на 3 и округляем в большую сторону
     */
    protected function calculateReportBalance(float $availableBalance): int
    {
        if ($availableBalance <= 0) {
            return 0;
        }

        return (int) ceil($availableBalance / 3);
    }

    /**
     * Генерирует имя из email (если username пустой)
     */
    protected function generateNameFromEmail(string $email): string
    {
        $parts = explode('@', $email);
        $localPart = $parts[0] ?? 'user';
        
        // Делаем первую букву заглавной
        return ucfirst($localPart);
    }

    /**
     * Импортирует одного пользователя
     */
    protected function importUser(array $userData, bool $skipExisting, bool $dryRun): string
    {
        $email = $userData['email'];
        
        // Проверяем существование пользователя
        $existingUser = User::where('email', $email)->first();
        $isUpdate = $existingUser !== null;

        if ($skipExisting && $isUpdate) {
            return 'skipped';
        }

        if ($dryRun) {
            $action = $isUpdate ? 'обновлен' : 'создан';
            $this->newLine();
            $this->line("  [DRY-RUN] Будет {$action}: {$userData['name']} ({$email}, report_balance: {$userData['report_balance']})");
            return $isUpdate ? 'updated' : 'created';
        }

        try {
            DB::beginTransaction();

            if ($isUpdate) {
                // Обновляем существующего пользователя (только report_balance)
                $existingUser->update([
                    'report_balance' => $userData['report_balance'],
                ]);
                
                DB::commit();
                return 'updated';
            } else {
                // Создаем нового пользователя
                // Генерируем случайный пароль (пользователь будет восстанавливать)
                $randomPassword = Str::random(32);
                
                User::create([
                    'name' => $userData['name'],
                    'email' => $email,
                    'password' => Hash::make($randomPassword),
                    'role' => 'user',
                    'balance' => 0,
                    'report_balance' => $userData['report_balance'],
                ]);
                
                DB::commit();
                return 'created';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
