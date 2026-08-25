<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressSnapshotRequest;
use App\Http\Resources\User\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    /**
     * Get all user addresses
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 4);
        $addresses = $request->user()
            ->addresses()->paginate($perPage);

        return AddressResource::collection($addresses);
    }
    /**
     * Get one user address
     */
    public function show(Address $address)
    {
        if (Gate::denies('access-address', $address)) {
            return response()->json([
                'message' => 'you are not authorized!'
            ], 403);
        }

        return new AddressResource($address);
    }

    /**
     * Store a user address
     */
    public function store(AddressSnapshotRequest $request)
    {
        $validated = $request->validated();

        $address = $request->user()
            ->addresses()->create($validated);

        return response()->json([
            'message' => "address created successfully.",
            'address' => new AddressResource($address)
        ], 201);
    }

    /**
     * Update a user address
     */
    public function update(AddressSnapshotRequest $request, Address $address)
    {
        if (Gate::denies('access-address', $address)) {
            return response()->json([
                'message' => 'you are not authorized!'
            ], 403);
        }

        $validated = $request->validated();

        $address->update($validated);

        return response()->json([
            'message' => "address updated successfully.",
            'address' => new AddressResource($address)
        ], 200);
    }

    /**
     * Destroy a user address
     */
    public function destroy(Address $address)
    {
        if (Gate::denies('access-address', $address)) {
            return response()->json([
                'message' => 'you are not authorized!'
            ], 403);
        }

        $address->delete();

        return response()->json([
            'message' => "address deleted successfully."
        ], 200);
    }
}
