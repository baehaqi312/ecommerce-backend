<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function() {

    require __DIR__ . '/api/user.php';
    require __DIR__ . '/api/product.php';
    require __DIR__ . '/api/cart.php';
    require __DIR__ . '/api/order.php';

    Route::get('dashboard', [App\Http\Controllers\Api\DashboardController::class, 'index']);
});

Route::get('products', [ProductController::class, 'index']);

Route::post('/login', 'Api\LoginController@index');
Route::post('/register', 'Api\LoginController@register');
Route::get('/logout', 'Api\LoginController@logout');
