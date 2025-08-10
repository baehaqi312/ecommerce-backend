<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $users = User::count();
        $products = Product::count();
        $transactions = Order::count();
        $transactionsAdmin = Order::where('payment_status', 'success')->sum('total_price');

        $order = Order::where('user_id', $user->id)->count();
        $orderSuccess = Order::where('user_id', $user->id)->where('payment_status', 'success')->count();

        return response()->json([
            'users' => $users,
            'products' => $products,
            'transactions' => $transactions,
            'transactions_admin' => $transactionsAdmin,
            'order_perngguna' => $order,
            'order_success' => $orderSuccess
        ]);
    }
}
