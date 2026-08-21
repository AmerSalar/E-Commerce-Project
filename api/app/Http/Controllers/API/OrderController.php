<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressSnapshotRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function orderNow(AddressSnapshotRequest $request)
    {
        $validatedAddress = $request->validated();

        // we first make sure cart has items
        $cart = $request->user()->cart?->load('items');
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'There is nothing to order, cart is empty!'
            ], 422);
        }

        // we add a transaction to safely do the actions
        $order = DB::transaction(function () use ($request, $cart, $validatedAddress) {
            // we lock products to avoid stock updates
            $productIds = $cart->items->pluck('id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');


            // validate that item is 'still' available in stock
            foreach ($cart->items as $item) {
                $cartQuantity = $item->pivot->quantity;
                $product = $products->get($item->id);

                if ($product->quantity < $cartQuantity) {
                    return response()->json([
                        'message' => "Sorry, {$product->name} is out of stock!",
                        'available_stock' => $product->quantity,
                        'in_cart' => $cartQuantity
                    ], 422);
                }
            }

            // create the order for the user, and calculate the total
            $order = Order::create([
                'user_id' => $request->user()->id,
                'address_snapshot' => $validatedAddress,
                'total' => $cart->total
            ]);

            // attach the order items from the cart items
            $pivotTableData = [];
            foreach ($cart->items as $item) {
                $pivotTableData[$item->id] = [
                    'item_name' => $item->name,
                    'item_price' => $item->price,
                    'quantity' => $item->pivot->quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // take the quantity from the stock
                $products->get($item->id)
                    ->decrement('quantity', $item->pivot->quantity);
            }
            $order->items()->attach($pivotTableData);

            // empty the cart
            $cart->items()->detach();

            return $order;
        });

        return response()->json([
            'message' => 'ordered successfully. order is now pending.',
            'order' => $order
        ]);
    }
}
