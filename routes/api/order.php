<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::get('orders', [OrderController::class, 'index']);
Route::get('orders/{order}', [OrderController::class, 'show']);
Route::post('checkout', [OrderController::class, 'checkout']);
Route::put('orders/{order}/status', [OrderController::class, 'updatePaymentStatus']);

Route::post('midtrans/notification', [OrderController::class, 'midtransCallback']);
