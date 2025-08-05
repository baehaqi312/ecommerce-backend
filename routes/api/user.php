<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Route CRUD untuk User
Route::get('users', [UserController::class, 'index']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);
