<?php

namespace App\Services;

use App\Http\Requests\Address\AddressSnapshotRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    public function orderNow(User $user, array $validatedAddress): array
    {
        return DB::transaction(function () use ($user, $validatedAddress) {

            // cart always exists, because user is authenticated user,
            // and every authenticated user has a cart.
            $cart = $user->cart?->loadRelations('items');

            // check for deleted products, which are null in cart items
            $staleItemsCount = DB::table('cart_items')->where('cart_id', $cart->id)
                ->whereNull('product_id')->delete();

            $note = $staleItemsCount > 0
                ? " We removed {$staleItemsCount} items from your order, because they just went out of stock."
                : "";
            $cart->loadRelations('items');

            // if after removing the stale items, the cart was empty
            // then it means there's nothing to order
            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'message' => "There's nothing to order, your cart is empty!{$note}",
                ]);
            }

            // we lock products to avoid stock updates
            $productIds = $cart->items->pluck('id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // attach the order items from the cart items
            $pivotTableData = [];
            // we need to calculate total based on locked products
            $total = 0.00;
            // validate that item is 'still' available in stock
            foreach ($cart->items as $item) {
                $cartQuantity = $item->pivot->quantity;
                $product = $products->get($item->id);
                if (!$product) {
                    throw ValidationException::withMessages([
                        'message' => "Sorry, {$item->name} just went out of stock!",
                    ]);
                }
                $total += $product->price * $cartQuantity;

                if ($product->quantity < $cartQuantity) {
                    throw ValidationException::withMessages([
                        "message" => "{$product->name} is out of stock, only {$product->quantity} is available in stock, your cart has {$cartQuantity}!",
                    ]);
                }


                $pivotTableData[$item->id] = [
                    'item_name' => $product->name,
                    'item_price' => $product->price,
                    'quantity' => $item->pivot->quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // take the quantity from the stock
                $product->decrement('quantity', $item->pivot->quantity);
            }

            // create the order for the user, and calculate the total
            $order = Order::create([
                'user_id' => $user->id,
                'address_snapshot' => $validatedAddress,
                'total' => $total,
                'status' => "pending"
            ]);

            $order->items()->attach($pivotTableData);

            // empty the cart
            $cart->items()->detach();

            return ["order" => $order, "note" => $note];
        });
    }

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::where('id', $order->id)
                ->lockForUpdate()
                ->first();

            if ($lockedOrder->status !== "pending") {
                throw ValidationException::withMessages([
                    'message' => "Cannot cancel this order, because it is {$lockedOrder->status}!"
                ]);
            }

            $productIds = $lockedOrder->items->pluck('id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($lockedOrder->items as $item) {
                $product = $products->get($item->id);
                if ($product) {
                    $product->increment('quantity', $item->pivot->quantity);
                }
            }

            $lockedOrder->update(['status' => 'cancelled']);
        });
    }

    public function deliverOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::where('id', $order->id)
                ->lockForUpdate()
                ->first();
            if ($lockedOrder->status !== "pending") {
                throw ValidationException::withMessages([
                    'message' => "Cannot deliver this order, because it is {$lockedOrder->status}!"
                ]);
            }

            $lockedOrder->update(['status' => 'delivered']);

            return $lockedOrder;
        });
    }
}
