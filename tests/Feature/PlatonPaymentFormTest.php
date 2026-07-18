<?php

use App\Http\Controllers\PlatonController;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

it('creates a signed Platon client server payment form without posting from the server', function () {
    Http::preventStrayRequests();

    config()->set('services.platon', [
        'enabled' => true,
        'sandbox' => true,
        'merchant_id' => 'merchant-key',
        'password' => 'merchant-password',
        'api_url' => 'https://secure.platononline.com/payment/auth',
        'callback_url' => 'https://example.com/payment/platon/callback',
        'result_url' => 'https://example.com/thank-you',
        'currency' => 'UAH',
        'language' => 'uk',
    ]);

    $order = Order::create([
        'email' => 'customer@example.com',
        'vin' => '1HGCM82633A004352',
        'report_type' => 'carfax',
        'currency' => 'uah',
        'locale' => 'en',
        'status' => 'pending payment',
        'total_price' => 100,
        'payment_method' => 'platon',
        'order_purpose' => 'report',
    ]);

    $response = (new PlatonController)->createPaymentLinkForOrder(
        $order,
        'carfax',
        'customer@example.com',
        '1HGCM82633A004352',
        'uah',
        'en'
    );

    expect($response['success'])->toBeTrue()
        ->and($response['payment_form']['action'])->toBe('https://secure.platononline.com/payment/auth')
        ->and($response['payment_form']['method'])->toBe('POST');

    $fields = $response['payment_form']['fields'];
    $dataPayload = json_decode(base64_decode($fields['data']), true);

    expect($fields)->toMatchArray([
        'key' => 'merchant-key',
        'payment' => 'CC',
        'order' => (string) $order->id,
        'email' => 'customer@example.com',
        'lang' => 'UK',
    ])->and($fields)->toHaveKeys(['data', 'url', 'sign'])
        ->and($fields['url'])->toContain('https://example.com/thank-you?id='.$order->id.'&key=')
        ->and($fields)->not->toHaveKey('req_token')
        ->and($dataPayload)->toMatchArray([
            'amount' => '100.00',
            'currency' => 'UAH',
            'description' => 'Report for VIN: 1HGCM82633A004352, Report type: carfax',
        ]);

    $expectedSign = md5(strtoupper(
        strrev($fields['key'])
        .strrev($fields['payment'])
        .strrev($fields['data'])
        .strrev($fields['url'])
        .strrev('merchant-password')
    ));

    expect($fields['sign'])->toBe($expectedSign);
});

it('syncs a pending Platon order status from the status API during polling', function () {
    $this->withoutMiddleware();

    Http::fake([
        'https://secure.platononline.com/post-unq/' => Http::response([
            'action' => 'GET_TRANS_STATUS_BY_ORDER',
            'result' => 'SUCCESS',
            'status' => 'SETTLED',
            'order_id' => '1',
            'amount' => '100.00',
            'trans_id' => '27242-45834-68448',
        ]),
    ]);

    config()->set('services.platon', [
        'enabled' => true,
        'sandbox' => true,
        'merchant_id' => 'merchant-key',
        'password' => 'merchant-password',
        'api_url' => 'https://secure.platononline.com/payment/auth',
        'status_url' => 'https://secure.platononline.com/post-unq/',
        'callback_url' => 'https://example.com/payment/platon/callback',
        'result_url' => 'https://example.com/thank-you',
        'currency' => 'UAH',
        'language' => 'uk',
    ]);

    $order = Order::create([
        'email' => 'customer@example.com',
        'currency' => 'uah',
        'status' => 'pending payment',
        'total_price' => 100,
        'payment_method' => 'platon',
    ]);

    $response = $this->postJson(route('order.check-status'), [
        'id' => $order->id,
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'status' => 'processing',
        ]);

    $order->refresh();

    expect($order->status)->toBe('processing')
        ->and($order->payment_data['platon_status_response']['status'])->toBe('SETTLED');

    Http::assertSent(function ($request) use ($order) {
        return $request->url() === 'https://secure.platononline.com/post-unq/'
            && $request['action'] === 'GET_TRANS_STATUS_BY_ORDER'
            && $request['client_key'] === 'merchant-key'
            && $request['order_id'] === (string) $order->id
            && $request['hash'] === md5(strtoupper('merchant-password'.$order->id));
    });
});

it('throttles Platon status API checks for pending orders', function () {
    $this->withoutMiddleware();

    Http::fake([
        'https://secure.platononline.com/post-unq/' => Http::response([
            'result' => 'ERROR',
            'error_message' => 'Transaction not found',
        ]),
    ]);

    config()->set('services.platon', [
        'enabled' => true,
        'sandbox' => true,
        'merchant_id' => 'merchant-key',
        'password' => 'merchant-password',
        'api_url' => 'https://secure.platononline.com/payment/auth',
        'status_url' => 'https://secure.platononline.com/post-unq/',
        'callback_url' => 'https://example.com/payment/platon/callback',
        'result_url' => 'https://example.com/thank-you',
        'currency' => 'UAH',
        'language' => 'uk',
    ]);

    $order = Order::create([
        'email' => 'customer@example.com',
        'currency' => 'uah',
        'status' => 'pending payment',
        'total_price' => 100,
        'payment_method' => 'platon',
    ]);

    $this->postJson(route('order.check-status'), ['id' => $order->id])->assertSuccessful();
    $this->postJson(route('order.check-status'), ['id' => $order->id])->assertSuccessful();

    $order->refresh();

    expect($order->status)->toBe('pending payment')
        ->and($order->payment_data['platon_status_response']['result'])->toBe('ERROR');

    Http::assertSentCount(1);
});
