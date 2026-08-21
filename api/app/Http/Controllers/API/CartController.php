<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
        $cart = $request->user()->cart->load('items');
        return new CartResource($cart);
    }
    public function push(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:50']]);
        // user desired quantity from form request or by default = 1
        $userQuantity = $request->integer('quantity', 1);

        // get already existing quantity if not then 0
        $cartItemQuantity = Auth::user()->cart?->items()
            ->where('cart_items.product_id', $product->id)
            ->value('cart_items.quantity') ?? 0;

        // syncWithoutDetaching is like update or insert,
        // either update existing value, or add new one
        $request->user()->cart->items()->syncWithoutDetaching([
            $product->id => ['quantity' => $userQuantity + $cartItemQuantity]
        ]);

        $cart = $request->user()->cart->load('items');
        return new CartResource($cart);
    }
}
