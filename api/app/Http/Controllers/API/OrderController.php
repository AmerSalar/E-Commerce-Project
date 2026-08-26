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
    public function __construct(protected DeliveryService $delivery) {}
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

        return new OrderCollection($orders);
    }
    /**
     * Get one of current user's orders
     */
    public function show(Order $order)
    {
        if (Gate::denies('my-order', $order)) {
            return response()->json([
                'message' => 'You are not authorized to access this order!'
            ], 403);
        }

        return new OrderResource($order->loadRelations('items'));
    }
    /**
     * Get all pending current user's orders
     */
    public function pending(Request $request)
    {
        $perPage = $request->query('perPage', 4);
        $orders = $request->user()->orders()
            ->where('status', 'pending')
            ->withRelations($request->query('include', 'items'))
            ->paginate($perPage);

        return new OrderCollection($orders);
    }

    /**
     * Order now
     */
    public function order(AddressSnapshotRequest $request)
    {
        $validatedAddress = $request->validated();

        $order = $this->delivery->orderNow($request->user(), $validatedAddress);

        return response()->json([
            'message' => 'ordered successfully. order is now pending.',
            'order' => new OrderResource($order->loadRelations('items'))
        ], 201);
    }

    /**
     * Cancel a pending order
     */
    public function cancel(Order $order)
    {
        if (Gate::denies('my-order', $order)) {
            return response()->json([
                'message' => 'You are not authorized to cancel this order!'
            ], 403);
        }

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
        if (Gate::denies('deliver-order')) {
            return response()->json([
                'message' => 'You are not authorized to deliver this order!'
            ], 403);
        }

        $order = $this->delivery->deliverOrder($order);

        return response()->json([
            'message' => 'Order delivered successfully.',
            'order' => new OrderResource($order->loadRelations('items'))
        ], 200);
    }
}
