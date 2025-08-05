<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

Route::get('carts/{user_id}', [CartController::class, 'index']);
Route::post('carts/add', [CartController::class, 'store']);
Route::delete('carts/remove/{cart_id}', [CartController::class, 'destroy']);
