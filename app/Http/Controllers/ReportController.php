<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function checkVin(Request $request)
    {
        $vin = $request->input('vin');
        if (empty($vin)) {
            return response()->json([
                'success' => false,
                'error' => __('index.vin_check.required'),
                'message' => __('index.vin_check.required'),
            ], 400);
        }
        if (strlen($vin) != 17) {
            return response()->json([
                'success' => false,
                'error' => __('index.vin_check.invalid_length_exact'),
                'message' => __('index.vin_check.invalid_length_exact'),
            ], 400);
        }
        $vin = strtoupper($vin);
        $vin = str_replace([' ', '-', '.'], '', $vin);

        // Verify hCaptcha token if hCaptcha is enabled.
        // If captcha was solved recently for this session, allow a short trust window.
        $hcaptchaSecret = config('hcaptcha.secret_key');
        $hcaptchaSiteKey = config('hcaptcha.site_key');
        $captchaTrustSeconds = max((int) config('hcaptcha.trust_seconds', 600), 60);
        $captchaTrustMaxChecks = max((int) config('hcaptcha.trust_max_checks', 10), 1);
        $captchaTrustKey = $this->captchaTrustKey($request);
        $captchaTrustCounterKey = $this->captchaTrustCounterKey($request);
        $captchaTrustedUntil = (int) Cache::store('redis')->get($captchaTrustKey, 0);
        $captchaChecksCount = (int) Cache::store('redis')->get($captchaTrustCounterKey, 0);
        $hasCaptchaTrust = $captchaTrustedUntil > now()->timestamp && $captchaChecksCount < $captchaTrustMaxChecks;

        if ($hcaptchaSiteKey && $hcaptchaSecret && !$hasCaptchaTrust) {
            $hcaptchaToken = $request->input('h-captcha-response');

            if (empty($hcaptchaToken)) {
                return response()->json([
                    'success' => false,
                    'captcha_required' => true,
                    'error' => __('index.vin_check.captcha_required'),
                    'message' => __('index.vin_check.captcha_required'),
                ], 400);
            }

            // Verify token with hCaptcha API
            $verifyResponse = Http::asForm()->post(config('hcaptcha.verify_url', 'https://api.hcaptcha.com/siteverify'), [
                'secret' => $hcaptchaSecret,
                'response' => $hcaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            $verifyData = $verifyResponse->json();

            if (!isset($verifyData['success']) || !$verifyData['success']) {
                Log::warning('hCaptcha verification failed', [
                    'vin' => $vin,
                    'error_codes' => $verifyData['error-codes'] ?? [],
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'captcha_required' => true,
                    'error' => __('index.vin_check.captcha_error'),
                    'message' => __('index.vin_check.captcha_error'),
                ], 400);
            }

            $captchaTrustedUntil = now()->addSeconds($captchaTrustSeconds)->timestamp;
            Cache::store('redis')->put($captchaTrustKey, $captchaTrustedUntil, now()->addSeconds($captchaTrustSeconds));
            Cache::store('redis')->put($captchaTrustCounterKey, 0, now()->addSeconds($captchaTrustSeconds));
            $captchaChecksCount = 0;
        }

        if ($hcaptchaSiteKey && $hcaptchaSecret) {
            $captchaChecksCount++;
            $remainingTrustSeconds = max($captchaTrustedUntil - now()->timestamp, 60);
            Cache::store('redis')->put($captchaTrustCounterKey, $captchaChecksCount, now()->addSeconds($remainingTrustSeconds));
        }

        $cacheKey = 'check_vin_'.$vin;

        return Cache::store('redis')->remember($cacheKey, now()->addMinutes(2), function () use ($vin) {
            $apiKey = config('services.monolith.key');
            $apiMonolithUrl = rtrim((string) config('services.monolith.url'), '/');

            try {
                $response = Http::withHeaders([
                    'API-KEY' => $apiKey,
                ])->withoutVerifying()->get($apiMonolithUrl.'/api/v4/check_report/?vin='.$vin);

                $statusCode = $response->status();
                $responseData = $response->json();

//                Log::info('VIN Check API Response', [
//                    'status' => $statusCode,
//                    'vin' => $vin,
//                    'response' => $responseData,
//                    'response_type' => gettype($responseData),
//                    'is_array' => is_array($responseData),
//                    'is_empty' => empty($responseData),
//                ]);

                // Проверяем статус код и наличие данных
                if ($statusCode >= 200 && $statusCode < 300) {
                    // Если responseData не пустой и это массив
                    if (! empty($responseData) && is_array($responseData)) {
                        // Если есть success: true или есть vin, считаем ответ успешным
                        $hasSuccess = isset($responseData['success']) && $responseData['success'] === true;
                        $hasVin = isset($responseData['vin']) && ! empty($responseData['vin']);

                        if ($hasSuccess || $hasVin) {
//                            Log::info('VIN Check: Returning success response', [
//                                'hasSuccess' => $hasSuccess,
//                                'hasVin' => $hasVin,
//                            ]);

                            // Возвращаем данные в новом формате
                            // Обрабатываем null значения, преобразуя их в false для булевых полей
                            $responseJson = [
                                'success' => $responseData['success'] ?? true,
                                'message' => $responseData['message'] ?? null,
                                'vin' => $responseData['vin'] ?? $vin,
                                'vehicle' => $responseData['vehicle'] ?? 'N/A',
                                'autocheck_records' => $responseData['autocheck_records'] ?? 0,
                                'carfax_records' => $responseData['carfax_records'] ?? 0,
                                'auction_record' => $responseData['auction_record'] ?? false,
                                'sticker_report' => $responseData['sticker_report'] ?? false,
                                'carfax_available' => $responseData['carfax_available'] ?? false,
                                'autocheck_available' => $responseData['autocheck_available'] ?? false,
                                'is_available' => $responseData['is_available'] ?? false,
                            ];

//                            Log::info('VIN Check: Sending response', ['response' => $responseJson]);

                            return response()->json($responseJson, 200);
                        }
                    }
                }

                // Если дошли сюда, значит что-то пошло не так
                Log::error('VIN Check failed', [
                    'status' => $statusCode,
                    'response' => $responseData,
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => __('index.vin_check.error'),
                    'message' => $responseData['message'] ?? __('index.vin_check.error'),
                    'is_available' => false,
                ], 400);

            } catch (\Exception $e) {
                Log::error('VIN Check exception', [
                    'error' => $e->getMessage(),
                    'vin' => $vin,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => __('index.vin_check.error'),
                    'message' => __('index.vin_check.error'),
                    'is_available' => false,
                ], 500);
            }
        });
    }

    public static function getVinReport($vin, $reportType)
    {
        $apiKey = config('services.monolith.key');
        $url = rtrim((string) config('services.monolith.url'), '/').'/api/v4/get_report';

        $response = Http::withHeaders([
            'API-KEY' => $apiKey,
        ])->timeout(120)->withoutVerifying()->get($url, [
            'vin' => $vin,
            'report_type' => $reportType,
        ]);

        if ($response->status() == 200) {
            return $response->json();
        } else {
            Log::error('API returned error', ['response' => $response]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get VIN report',
            ], $response->status());
        }
    }

    public function viewReport($report_key)
    {
        $order = Order::where('report_key', $report_key)->first();
        if (! $order) {
            return redirect()->to(\App\Support\LocaleRoute::route('index'));
        }

        $cacheKey = 'report_'.$report_key;

        try {
            return Cache::remember($cacheKey, now()->addHours(12), function () use ($order) {
                return $this->buildReportResponse($order->report_key);
            });
        } catch (\Throwable $e) {
            Log::error('ReportController: Failed to cache report response', [
                'order_id' => $order->id,
                'report_key' => $order->report_key,
                'error' => $e->getMessage(),
            ]);

            return $this->buildReportResponse($order->report_key);
        }
    }

    private function buildReportResponse(string $reportKey)
    {
        $apiKey = config('services.monolith.key');
        $url = rtrim((string) config('services.monolith.url'), '/').'/api/v4/view_report/?report_key='.$reportKey;
        $response = Http::withHeaders([
            'API-KEY' => $apiKey,
        ])->withoutVerifying()->get($url);

        $base64 = $response->json()['base64'];
        $html = base64_decode($base64);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    private function captchaTrustKey(Request $request): string
    {
        $sessionId = '';
        if ($request->hasSession()) {
            $sessionId = (string) $request->session()->getId();
        }

        $signature = $sessionId . '|' . $request->ip() . '|' . (string) $request->userAgent();

        return 'vin_check_hcaptcha_trust:' . sha1($signature);
    }

    private function captchaTrustCounterKey(Request $request): string
    {
        $sessionId = '';
        if ($request->hasSession()) {
            $sessionId = (string) $request->session()->getId();
        }

        $signature = $sessionId . '|' . $request->ip() . '|' . (string) $request->userAgent();

        return 'vin_check_hcaptcha_trust_checks:' . sha1($signature);
    }
}
