<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Cart;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'items.product')->get();
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('user', 'items.product')->find($id);
        return response()->json($order);
    }
    // app/Http/Controllers/Api/OrderController.php
    public function checkout(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);

        $carts = Cart::where('user_id', $request->user_id)->with('product')->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 400);
        }

        $totalPrice = 0;
        $itemDetails = [];

        foreach ($carts as $cart) {
            $totalPrice += $cart->product->price * $cart->qty;
            $itemDetails[] = [
                'id' => $cart->product->id,
                'price' => $cart->product->price,
                'quantity' => $cart->qty,
                'name' => $cart->product->name,
            ];
        }

        // 1. Simpan order ke database
        $order = Order::create([
            'user_id' => $request->user_id,
            'total_price' => $totalPrice,
            'order_id' => 'ORDER-' . time() . '-' . rand(1000, 9999),
        ]);

        // 2. Simpan item dari keranjang ke tabel order_items
        foreach ($carts as $cart) {
            $order->items()->create([
                'product_id' => $cart->product_id,
                'qty' => $cart->qty,
                'price' => $cart->product->price,
            ]);
        }

        // ... (Bagian konfigurasi Midtrans, sama seperti sebelumnya) ...

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => $totalPrice,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $order->update(['snap_token' => $snapToken]);

        Cart::where('user_id', $request->user_id)->delete();

        return response()->json([
            'status' => 'success',
            'order' => $order,
            'snap_token' => $snapToken,
        ]);
    }

    public function midtransCallback(Request $request)
    {
        // Ambil server key dari konfigurasi
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status;
        $orderId = $notif->order_id;

        $order = Order::where('order_id', $orderId)->first();

        if ($order) {
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['payment_status' => 'challenge']);
                } else if ($fraudStatus == 'accept') {
                    $order->update(['payment_status' => 'success']);
                }
            } else if ($transactionStatus == 'settlement') {
                $order->update(['payment_status' => 'success']);
            } else if ($transactionStatus == 'pending') {
                $order->update(['payment_status' => 'pending']);
            } else if ($transactionStatus == 'deny') {
                $order->update(['payment_status' => 'failed']);
            } else if ($transactionStatus == 'expire') {
                $order->update(['payment_status' => 'failed']);
            } else if ($transactionStatus == 'cancel') {
                $order->update(['payment_status' => 'failed']);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
