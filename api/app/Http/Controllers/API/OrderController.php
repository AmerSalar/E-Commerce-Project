<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressSnapshotRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * Get all current user's orders
     */
    public function getAll(Request $request)
    {
        $perPage = $request->query('perPage', 4);
        $orders = $request->user()->orders()
            ->with('items')->paginate($perPage);
        if ($orders->isEmpty()) {
            return response()->noContent();
        }

        return new OrderCollection($orders);
    }
    /**
     * Get one of current user's orders
     */
    public function getOne(Order $order)
    {
        if (Gate::denies('my-order', $order)) {
            return response()->json([
                'message' => 'You are not authorized to access this order!'
            ], 403);
        }

        return new OrderResource($order->load('items'));
    }
    /**
     * Get all pending current user's orders
     */
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

    /**
     * Order now
     */
    public function orderNow(AddressSnapshotRequest $request)
    {
        $validatedAddress = $request->validated();


        try {
            // we add a transaction to safely do the actions
            $order = DB::transaction(function () use ($request, $validatedAddress) {

                // we first make sure cart has items
                $cart = $request->user()->cart?->load('items');
                if (!$cart || $cart->items->isEmpty()) {
                    abort(response()->json([
                        'message' => 'There is nothing to order, cart is empty!'
                    ], 422));
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
                        abort(response()->json([
                            'message' => "Sorry, {$item->name} just went out of stock!",
                        ], 422));
                    }
                    $total += $product->price * $cartQuantity;

                    if ($product->quantity < $cartQuantity) {
                        abort(response()->json([
                            'message' => "Sorry, {$product->name} is out of stock!",
                            'available_stock' => $product->quantity,
                            'in_cart' => $cartQuantity
                        ], 422));
                    }


                    $pivotTableData[$item->id] = [
                        'item_name' => $item->name,
                        'item_price' => $item->price,
                        'quantity' => $item->pivot->quantity,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    // take the quantity from the stock
                    $product->decrement('quantity', $item->pivot->quantity);
                }

                // create the order for the user, and calculate the total
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'address_snapshot' => $validatedAddress,
                    'total' => $total,
                    'status' => "pending"
                ]);

                $order->items()->attach($pivotTableData);

                // empty the cart
                $cart->items()->detach();

                return $order;
            });
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }

        return response()->json([
            'message' => 'ordered successfully. order is now pending.',
            'order' => new OrderResource($order->load('items'))
        ]);
    }

    /**
     * Cancel a pending order
     */
    public function cancelOrder(Request $request, Order $order)
    {
        if (Gate::denies('my-order', $order)) {
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
                $product = $products->get($item->id);
                if ($product) {
                    $product->increment('quantity', $item->pivot->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        return response()->json([
            'message' => 'order cancelled successfully.'
        ], 200);
    }

    /**
     * Check order as delivered
     */
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
}
