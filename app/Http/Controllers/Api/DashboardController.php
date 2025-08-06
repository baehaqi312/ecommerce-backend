<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::count();
        $products = Product::count();
        $transactions = Order::count();
        $transactionsAdmin = Order::where('payment_status', 'success')->sum('total_price');

        return response()->json([
            'users' => $users,
            'products' => $products,
            'transactions' => $transactions,
            'transactions_admin' => $transactionsAdmin,
        ]);
    }
}
