<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->user()->cart->items()->attach($product);

        $cart = $request->user()->cart->load('items');
        return new CartResource($cart);
    }
}
