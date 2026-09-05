<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressSnapshotRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\DeliveryService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function __construct(protected DeliveryService $delivery)
    {
    }
    /**
     * Get all current user's orders
     */
    public function index(Request $request)
    {
        $perPage = max(1, min($request->query('perPage', 4), 50));
        $orders = $request->user()->orders()
            ->withRelations($request->query('include', 'items'))
            ->latest()
            ->paginate($perPage);

        return response()->json(['orders' => OrderResource::collection($orders)], 200);
    }
    /**
     * Get one of current user's orders
     */
    public function show(Order $order)
    {
        Gate::authorize('my-order', $order);

        return new OrderResource($order->loadRelations('items'));
    }
    /**
     * Get all current user's pending orders
     */
    public function pending(Request $request)
    {
        $perPage = $request->query('perPage', 4);
        $orders = $request->user()->orders()
            ->where('status', 'pending')
            ->withRelations($request->query('include', 'items'))
            ->paginate($perPage);

        return response()->json(['orders' => OrderResource::collection($orders)], 200);
    }

    /**
     * Order now
     */
    public function order(AddressSnapshotRequest $request)
    {
        $validatedAddress = $request->validated();

        $response = $this->delivery->orderNow($request->user(), $validatedAddress);
        $order = $response['order'];
        $note = $response['note'];

        return response()->json([
            'message' => "ordered successfully. order is now pending.{$note}",
            'order' => new OrderResource($order->loadRelations('items'))
        ], 201);
    }

    /**
     * Cancel a pending order
     */
    public function cancel(Order $order)
    {
        Gate::authorize('my-order', $order);

        $this->delivery->cancelOrder($order);

        return response()->json([
            'message' => 'order cancelled successfully.'
        ], 200);
    }

    /**
     * Check order as delivered
     */
    public function deliver(Order $order)
    {
        Gate::authorize('deliver-order');

        $order = $this->delivery->deliverOrder($order);

        return response()->json([
            'message' => "Order delivered successfully.",
            'order' => new OrderResource($order->loadRelations('items'))
        ], 200);
    }
}
