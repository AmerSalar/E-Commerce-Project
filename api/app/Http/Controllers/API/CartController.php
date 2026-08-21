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

        $cart = $request->user()->cart()->firstOrCreate();

        // get already existing quantity if not then 0
        $cartItemQuantity = $cart->items()
            ->where('cart_items.product_id', $product->id)
            ->value('cart_items.quantity') ?? 0;

        $totalWantedQuantity = $userQuantity + $cartItemQuantity;
        if ($totalWantedQuantity > $product->quantity) {
            return response()->json([
                'message' => "Only {$product->quantity} in stock, failed to add to cart!",
                'desired_quantity' => $totalWantedQuantity,
                'currently_in_cart' => $cartItemQuantity
            ], 422);
        }

        // syncWithoutDetaching is like update or insert,
        // either update existing value, or add new one
        $cart->items()->syncWithoutDetaching([
            $product->id => ['quantity' => $totalWantedQuantity]
        ]);

        return new CartResource($cart->load('items'));
    }

    public function pull(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:50']]);
        // user desired quantity from form request or by default = 1
        $userQuantity = $request->integer('quantity', 1);

        $cart = $request->user()->cart()->firstOrCreate();

        $cartItemQuantity = $cart->items()
            ->where('cart_items.product_id', $product->id)
            ->value('cart_items.quantity') ?? 0;

        if (($cartItemQuantity - $userQuantity) < 0) {
            return response()->json([
                'message' => "Only {$cartItemQuantity} in cart, failed to pull from cart!",
                'desired_quantity' => $userQuantity,
                'currently_in_cart' => $cartItemQuantity
            ], 422);
        }
    }
}
