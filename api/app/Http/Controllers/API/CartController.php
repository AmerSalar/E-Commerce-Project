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
        $user = $request->user();
        $cart = $user->cart->load('items');
        return new CartResource($cart);
    }
    public function push(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:50']]);
        $quantity = $request->input('quantity') ?? 1;
        $cartItemQuantity = 0;

        if ($request->user()->cart->items()->where('product_id', $product)->exists()) {

            $cartItemQuantity = DB::table('cart_items')->whereRaw(
                'cart_id = ? AND product_id = ?',
                [
                    $request->user()->cart->id,
                    $product->id
                ]
            )->first()->quantity;
        }


        $request->user()->cart->items()->syncWithoutDetaching([
            $product->id => ['quantity' => $quantity + $cartItemQuantity]
        ]);


        $cart = $request->user()->cart->load('items');
        return new CartResource($cart);
    }
}
