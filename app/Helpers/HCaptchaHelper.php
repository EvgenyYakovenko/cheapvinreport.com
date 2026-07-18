<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HCaptchaHelper
{
    /**
     * Проверить hCaptcha токен
     *
     * @param Request $request
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function verify(Request $request): array
    {
        $hcaptchaSecret = config('hcaptcha.secret_key');
        $hcaptchaSiteKey = config('hcaptcha.site_key');

        // Если hCaptcha не настроен, пропускаем проверку
        if (!$hcaptchaSiteKey || !$hcaptchaSecret) {
            return ['success' => true, 'error' => null];
        }

        $hcaptchaToken = $request->input('h-captcha-response');

        if (empty($hcaptchaToken)) {
            return [
                'success' => false,
                'error' => 'hCaptcha verification required',
            ];
        }

        // Verify token with hCaptcha API
        $verifyResponse = Http::asForm()->post(
            config('hcaptcha.verify_url', 'https://api.hcaptcha.com/siteverify'),
            [
                'secret' => $hcaptchaSecret,
                'response' => $hcaptchaToken,
                'remoteip' => $request->ip(),
            ]
        );

        $verifyData = $verifyResponse->json();

        if (!isset($verifyData['success']) || !$verifyData['success']) {
            $errorCodes = $verifyData['error-codes'] ?? [];
            
            Log::warning('hCaptcha verification failed', [
                'error_codes' => $errorCodes,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            // Определяем более конкретную ошибку
            $errorMessage = 'hCaptcha verification failed';
            if (in_array('invalid-input-response', $errorCodes)) {
                $errorMessage = 'Invalid hCaptcha token. Please try again.';
            } elseif (in_array('invalid-input-secret', $errorCodes)) {
                $errorMessage = 'hCaptcha configuration error';
            } elseif (in_array('timeout-or-duplicate', $errorCodes)) {
                $errorMessage = 'hCaptcha token expired. Please try again.';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        }

        // Дополнительная проверка: если hCaptcha вернул success, но есть подозрительные признаки
        // (для Pro плана можно использовать score, но в базовом плане его нет)
        
        return ['success' => true, 'error' => null];
    }
}
