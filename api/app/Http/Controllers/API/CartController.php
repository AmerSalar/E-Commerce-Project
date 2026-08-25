<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Product;
use App\Services\CartShoppingService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartShoppingService $cartShopping) {}
    /**
     * get current user's cart
     */
    public function index(Request $request)
    {
        $cart = $request->user()->cart;
        if (!$cart) {
            return response()->json([
                'message' => "cart was not found for this user!"
            ], 404);
        }
        return new CartResource($cart->load('items'));
    }
    /**
     * push item into user's cart
     */
    public function push(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:50']]);

        // user desired quantity from form request or by default = 1
        $userQuantity = $request->integer('quantity') ?: 1;

        $cart = $this->cartShopping
            ->handlePushItem($request->user(), $product, $userQuantity);

        return response()->json([
            'message' => "Item pushed into cart successfully.",
            'cart' => new CartResource($cart->load('items'))
        ]);
    }

    /**
     * pull item from user's cart
     */
    public function pull(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:50']]);
        // user desired quantity from form request or by default = 1
        $userQuantity = $request->integer('quantity') ?: 1;

        $cart = $this->cartShopping
            ->handlePullItem($request->user(), $product, $userQuantity);

        return response()->json([
            'message' => "Item pulled from cart successfully.",
            'cart' => new CartResource($cart->load('items'))
        ]);
    }

    /**
     * abandon/reset current user's cart
     */
    public function abandon(Request $request)
    {
        $cart = $request->user()->cart;
        if (!$cart) {
            return response()->json([
                'message' => "cart was not found for this user!"
            ], 404);
        }
        $cart->items()->detach();

        return response()->json([
            'message' => 'Cart abandoned, and reset to empty.',
        ], 200);
    }
}
