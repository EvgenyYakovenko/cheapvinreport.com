<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $invoiceId)
 */
class Order extends Model
{
    protected $fillable = [
        'email',
        'vin',
        'report_type',
        'report_key',
        'signature',
        'status',
        'total_price',
        'payment_data',
        'order_purpose',
        'report_to_add',
        'payment_method',
        'currency',
        'locale',
        'invoice_id',
        'order_key',
    ];

    protected $casts = [
        'payment_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // Генерируем order_key после создания заказа
        static::created(function ($order) {
            if (!$order->order_key) {
                $order->generateOrderKey();
            }
        });
    }

    /**
     * Генерирует уникальный ключ заказа на основе email, created_at и id
     */
    public function generateOrderKey()
    {
        if (!$this->id) {
            return; // Нельзя генерировать ключ без id
        }
        
        $secret = config('app.key', 'default-secret-key');
        $data = ($this->email ?? '') . '|' . ($this->created_at ? $this->created_at->toIso8601String() : now()->toIso8601String()) . '|' . $this->id;
        $hash = hash_hmac('sha256', $data, $secret);
        // Берем первые 16 символов для короткого формата
        $this->order_key = substr(base64_encode($hash), 0, 16);
        $this->saveQuietly(); // Сохраняем без вызова событий, чтобы избежать рекурсии
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
