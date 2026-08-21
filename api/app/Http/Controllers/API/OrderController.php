<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderNow(Request $request)
    {
        $cart = $request->user()->cart?->load('items');
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'There is nothing to order, cart is empty!'
            ], 422);
        }
    }
}
