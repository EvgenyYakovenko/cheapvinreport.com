<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup {--dry-run : Тестовый запуск без удаления заказов}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет старые заказы: pending payment/expired старше 2 дней и failed старше 7 дней';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('⚠️  РЕЖИМ ТЕСТОВОГО ЗАПУСКА (DRY RUN) - заказы не будут удалены');
            $this->newLine();
        }
        
        $this->info('Запуск очистки старых заказов...');

        // Удаляем заказы со статусами "pending payment" и "expired" старше 2 дней
        $pendingExpiredCutoff = now()->subDays(2);
        $pendingExpiredOrders = Order::whereIn('status', ['pending payment', 'expired'])
            ->where('created_at', '<', $pendingExpiredCutoff)
            ->orderBy('created_at', 'asc')
            ->get();

        $pendingExpiredCount = $pendingExpiredOrders->count();
        
        if ($pendingExpiredCount > 0) {
            $this->info("Найдено заказов 'pending payment' или 'expired' старше 2 дней: {$pendingExpiredCount}");
            
            if ($isDryRun) {
                $this->displayOrdersTable($pendingExpiredOrders, 'pending payment/expired');
            } else {
                foreach ($pendingExpiredOrders as $order) {
                    $order->delete();
                }
                
                $this->info("✓ Удалено заказов 'pending payment' или 'expired': {$pendingExpiredCount}");
                Log::info("CleanupOrders: Удалено {$pendingExpiredCount} заказов со статусами 'pending payment' или 'expired' старше 2 дней");
            }
        } else {
            $this->info("Заказов 'pending payment' или 'expired' старше 2 дней не найдено");
        }

        // Удаляем заказы со статусом "failed" старше недели
        $failedCutoff = now()->subWeek();
        $failedOrders = Order::where('status', 'failed')
            ->where('created_at', '<', $failedCutoff)
            ->orderBy('created_at', 'asc')
            ->get();

        $failedCount = $failedOrders->count();
        
        if ($failedCount > 0) {
            $this->info("Найдено заказов 'failed' старше недели: {$failedCount}");
            
            if ($isDryRun) {
                $this->displayOrdersTable($failedOrders, 'failed');
            } else {
                foreach ($failedOrders as $order) {
                    $order->delete();
                }
                
                $this->info("✓ Удалено заказов 'failed': {$failedCount}");
                Log::info("CleanupOrders: Удалено {$failedCount} заказов со статусом 'failed' старше недели");
            }
        } else {
            $this->info("Заказов 'failed' старше недели не найдено");
        }

        $totalToDelete = $pendingExpiredCount + $failedCount;
        
        if ($totalToDelete > 0) {
            if ($isDryRun) {
                $this->newLine();
                $this->warn("Всего будет удалено заказов: {$totalToDelete}");
                $this->info("Для реального удаления запустите команду без опции --dry-run");
            } else {
                $this->info("Всего удалено заказов: {$totalToDelete}");
            }
        } else {
            $this->info("Нет заказов для удаления");
        }

        return Command::SUCCESS;
    }

    /**
     * Отображает таблицу с информацией о заказах
     */
    protected function displayOrdersTable($orders, string $status): void
    {
        $this->newLine();
        $this->line("Заказы со статусом '{$status}', которые будут удалены:");
        
        $tableData = [];
        foreach ($orders as $order) {
            $tableData[] = [
                'ID' => $order->id,
                'Email' => $order->email ?? '-',
                'VIN' => $order->vin ?? '-',
                'Создан' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-',
                'Возраст' => $order->created_at ? $order->created_at->diffForHumans() : '-',
            ];
        }
        
        $this->table(['ID', 'Email', 'VIN', 'Создан', 'Возраст'], $tableData);
        $this->newLine();
    }
}
