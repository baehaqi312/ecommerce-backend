<?php

namespace App\Http\Controllers\Api;

use App\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index($user_id)
    {
        $carts = Cart::where('user_id', $user_id)->with('product')->get();
        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            // Jika produk sudah ada di keranjang, tambahkan kuantitasnya
            $cart->increment('qty', $request->qty);
        } else {
            // Jika belum, buat item keranjang baru
            $cart = Cart::create($request->all());
        }

        return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang.', 'cart' => $cart]);
    }

    public function destroy($cart_id)
    {
        $cart = Cart::find($cart_id);
        if (!$cart) {
            return response()->json(['message' => 'Item keranjang tidak ditemukan.'], 404);
        }

        $cart->delete();
        return response()->json(['message' => 'Item keranjang berhasil dihapus.']);
    }
}
