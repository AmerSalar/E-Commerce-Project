<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * get current user's cart
     */
    public function getCart(Request $request)
    {
        $cart = $request->user()->cart()->firstOrCreate()->load('items');
        return new CartResource($cart);
    }
    /**
     * push item into user's cart
     */
    public function push(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:50']]);
        // user desired quantity from form request or by default = 1
        $userQuantity = $request->integer('quantity', 1);

        try {
            $cart = DB::transaction(function () use ($request, $product, $userQuantity) {

                // Lock will be on product record until transaction ends.
                $lockedProduct = Product::where('id', $product->id)
                    ->lockForUpdate()
                    ->first();

                $cart = $request->user()->cart()->firstOrCreate();

                // get already existing quantity if not then 0
                $cartItemQuantity = $cart->items()
                    ->where('cart_items.product_id', $lockedProduct->id)
                    ->value('cart_items.quantity') ?? 0;

                $totalWantedQuantity = $userQuantity + $cartItemQuantity;
                if ($totalWantedQuantity > $lockedProduct->quantity) {
                    abort(response()->json([
                        'message' => "Only {$lockedProduct->quantity} in stock, failed to add to cart!",
                        'desired_quantity' => $totalWantedQuantity,
                        'currently_in_cart' => $cartItemQuantity
                    ], 422));
                }

                $product->save();

                // syncWithoutDetaching is like update or insert,
                // either update existing value, or add new one
                $cart->items()->syncWithoutDetaching([
                    $lockedProduct->id => ['quantity' => $totalWantedQuantity]
                ]);

                return $cart;
            });
        } catch (HttpResponseException $e) {
            // for the custom error we have inside the DB transaction
            return $e->getResponse();
        }

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
        $userQuantity = $request->integer('quantity', 1);

        try {
            $cart = DB::transaction(function () use ($request, $product, $userQuantity) {

                $lockedProduct = Product::where('id', $product->id)
                    ->lockForUpdate()
                    ->first();

                $cart = $request->user()->cart()->firstOrCreate();

                $cartItemQuantity = $cart->items()
                    ->where('cart_items.product_id', $lockedProduct->id)
                    ->value('cart_items.quantity') ?? 0;
                if ($cartItemQuantity === 0) {
                    abort(response()->json([
                        'message' => "This item does not exist inside the cart!"
                    ], 422));
                }

                $calculatedQuantity = $cartItemQuantity - $userQuantity;
                if ($calculatedQuantity < 0) {
                    abort(response()->json([
                        'message' => "Only {$cartItemQuantity} in cart, failed to pull from cart!",
                        'desired_quantity' => $userQuantity,
                        'currently_in_cart' => $cartItemQuantity
                    ], 422));
                }
                if ($calculatedQuantity === 0) {
                    $cart->items()->detach($lockedProduct->id);
                } else {
                    $cart->items()->syncWithoutDetaching([
                        $lockedProduct->id => ['quantity' => $calculatedQuantity]
                    ]);
                }

                return $cart;
            });
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }


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
        $cart = $request->user()->cart()->firstOrCreate();
        $cart->items()->detach();

        return response()->json([
            'message' => 'Cart abandoned, and reset to empty.',
        ], 200);
    }

    public static function notFound()
    {
        return response()->json([
            'message' => 'Product not found!'
        ], 404);
    }
}
