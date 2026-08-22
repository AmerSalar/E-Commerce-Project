<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressSnapshotRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function getPendingOrders(Request $request)
    {
        $perPage = $request->query('perPage', 4);
        $orders = $request->user()->orders()
            ->where('status', 'pending')->with('items')->paginate($perPage);

        if ($orders->isEmpty()) {
            return response()->noContent();
        }

        return new OrderCollection($orders);
    }
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
                'total' => $cart->total,
                'status' => "pending"
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
            'order' => new OrderResource($order->load('items'))
        ]);
    }

    public function cancelOrder(Request $request, Order $order)
    {
        if (Gate::denies('cancel-order', $order)) {
            return response()->json([
                'message' => 'You are not authorized to cancel this order!'
            ], 403);
        }

        if ($order->status !== "pending") {
            return response()->json([
                'message' => "Cannot cancel this order, because it is {$order->status}!"
            ], 422);
        }

        DB::transaction(function () use ($order) {
            $productIds = $order->items->pluck('id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($order->items as $item) {
                $products->get($item->id)
                    ->increment('quantity', $item->pivot->quantity);
            }

            $order->update(['status' => 'cancelled']);
        });

        return response()->json([
            'message' => 'order cancelled successfully.'
        ], 200);
    }

    public function deliverOrder(Order $order)
    {
        if (Gate::denies('deliver-order')) {
            return response()->json([
                'message' => 'You are not authorized to deliver this order!'
            ], 403);
        }

        if ($order->status !== "pending") {
            return response()->json([
                'message' => "Cannot deliver this order, because it is {$order->status}!"
            ], 422);
        }

        $order->update(['status' => 'delivered']);

        return response()->json([
            'message' => 'Order delivered successfully.',
            'order' => new OrderResource($order->load('items'))
        ], 200);
    }
    public static function notFound()
    {
        return response()->json([
            'message' => 'Order not found!'
        ], 404);
    }
}
