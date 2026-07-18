<?php

use App\Http\Controllers\MonobankController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'monobank'], function () {
        Route::post('/check-order-status', [MonobankController::class, 'checkOrderStatus']);
    });
});
