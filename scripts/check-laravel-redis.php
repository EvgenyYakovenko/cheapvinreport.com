<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

function out(string $line): void
{
    echo $line.PHP_EOL;
}

function boolText(bool $value): string
{
    return $value ? 'yes' : 'no';
}

function mask(mixed $value): string
{
    if ($value === null || $value === '') {
        return '(empty)';
    }

    $string = (string) $value;
    $length = strlen($string);

    if ($length <= 2) {
        return str_repeat('*', $length);
    }

    return substr($string, 0, 1).str_repeat('*', $length - 2).substr($string, -1);
}

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

out('=== Laravel Redis Check ===');
out('php_version: '.PHP_VERSION);
out('phpredis_extension_loaded: '.boolText(extension_loaded('redis')));
out('predis_class_available: '.boolText(class_exists('Predis\\Client')));
out('cache_default_store: '.(string) config('cache.default'));
out('cache_redis_connection: '.(string) config('cache.stores.redis.connection'));
out('redis_client: '.(string) config('database.redis.client'));
out('redis_default_host: '.(string) config('database.redis.default.host'));
out('redis_default_port: '.(string) config('database.redis.default.port'));
out('redis_default_db: '.(string) config('database.redis.default.database'));
out('redis_default_password: '.mask(config('database.redis.default.password')));
out('redis_cache_db: '.(string) config('database.redis.cache.database'));
out('');

try {
    $pong = Redis::connection('default')->ping();
    out('redis_connection_default_ping: OK ('.(is_scalar($pong) ? (string) $pong : gettype($pong)).')');
} catch (Throwable $e) {
    out('redis_connection_default_ping: FAIL ('.$e->getMessage().')');
}

try {
    $pong = Redis::connection('cache')->ping();
    out('redis_connection_cache_ping: OK ('.(is_scalar($pong) ? (string) $pong : gettype($pong)).')');
} catch (Throwable $e) {
    out('redis_connection_cache_ping: FAIL ('.$e->getMessage().')');
}

out('');

try {
    $key = 'health:cache:redis:'.uniqid('', true);
    Cache::store('redis')->put($key, 'ok', now()->addMinutes(1));
    $value = Cache::store('redis')->get($key);
    Cache::store('redis')->forget($key);
    out('cache_store_redis_put_get: '.($value === 'ok' ? 'OK' : 'FAIL (unexpected value)'));
} catch (Throwable $e) {
    out('cache_store_redis_put_get: FAIL ('.$e->getMessage().')');
}

try {
    $defaultStore = (string) config('cache.default');
    $key = 'health:cache:default:'.uniqid('', true);
    Cache::put($key, 'ok', now()->addMinutes(1));
    $value = Cache::get($key);
    Cache::forget($key);
    out('cache_store_default_put_get ('.$defaultStore.'): '.($value === 'ok' ? 'OK' : 'FAIL (unexpected value)'));
} catch (Throwable $e) {
    out('cache_store_default_put_get: FAIL ('.$e->getMessage().')');
}

out('');
out('done');
