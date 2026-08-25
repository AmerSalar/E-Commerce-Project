<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CartShoppingService
{
    public function handlePushItem(User $user, Product $product, int $userQuantity): Cart
    {
        return DB::transaction(function () use ($user, $product, $userQuantity) {

            // Lock will be on product record until transaction ends.
            $lockedProduct = Product::where('id', $product->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProduct) {
                throw new ModelNotFoundException("Product not found!");
            }

            $cart = $user->cart;
            if (!$cart) {
                throw new ModelNotFoundException("Cart not found!");
            }

            // get already existing quantity if not then 0
            $cartItemQuantity = $cart->items()
                ->where('cart_items.product_id', $lockedProduct->id)
                ->value('cart_items.quantity') ?? 0;

            $totalWantedQuantity = $userQuantity + $cartItemQuantity;
            if ($totalWantedQuantity > $lockedProduct->quantity) {

                throw ValidationException::withMessages([
                    'message' => "Only {$lockedProduct->quantity} in stock, failed to add to cart!",
                    'desired_quantity' => $totalWantedQuantity,
                    'currently_in_cart' => $cartItemQuantity
                ]);
            }

            // syncWithoutDetaching is like update or insert,
            // either update existing value, or add new one
            $cart->items()->syncWithoutDetaching([
                $lockedProduct->id => ['quantity' => $totalWantedQuantity]
            ]);

            return $cart;
        });
    }


    public function handlePullItem(User $user, Product $product, int $userQuantity): Cart
    {
        return DB::transaction(function () use ($user, $product, $userQuantity) {

            $lockedProduct = Product::where('id', $product->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProduct) {
                throw new ModelNotFoundException("Product not found!");
            }

            $cart = $user->cart;
            if (!$cart) {
                throw new ModelNotFoundException("Cart not found!");
            }

            $cartItemQuantity = $cart->items()
                ->where('cart_items.product_id', $lockedProduct->id)
                ->value('cart_items.quantity') ?? 0;
            if ($cartItemQuantity === 0) {
                throw ValidationException::withMessages([
                    'message' => "This item does not exist inside the cart!"
                ]);
            }

            $calculatedQuantity = $cartItemQuantity - $userQuantity;
            if ($calculatedQuantity < 0) {
                throw ValidationException::withMessages([
                    'message' => "Only {$cartItemQuantity} in cart, failed to pull from cart!",
                    'desired_quantity' => $userQuantity,
                    'currently_in_cart' => $cartItemQuantity
                ]);
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
    }
}
