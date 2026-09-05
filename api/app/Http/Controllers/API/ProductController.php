<?php

namespace App\Http\Controllers\API;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\SaveProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(protected InventoryService $inventory)
    {
    }
    /**
     * get all products
     */
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('perPage', 10), 50));
        $products = Product::query()
            ->withRelations($request->query('include'))
            // this is ORDER BY id DESC
            ->latest('id')
            ->paginate($perPage);
        return ProductResource::collection($products);
    }
    /**
     * get one product
     */
    public function show(Request $request, Product $product)
    {
        return new ProductResource(
            $product->loadRelations($request->query('include'))
        );
    }
    /**
     * store a new product
     */
    public function store(SaveProductRequest $request)
    {
        Gate::authorize('manage');

        $validated = $request->validated();
        $picture = $request->file('picture');

        $product = $this->inventory->storeProduct($picture, $validated);

        return response()->json([
            'message' => 'product created successfully.',
            'product' => new ProductResource($product->loadRelations('categories'))
        ], 201);
    }
    /**
     * update a product
     */
    public function update(SaveProductRequest $request, Product $product)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to update this product!',
            ], 403);
        }

        $validated = $request->validated();
        $picture = $request->file('picture');

        $this->inventory->updateProduct($product, $picture, $validated);

        return response()->json([
            'message' => 'product updated successfully.',
            'product' => new ProductResource($product->loadRelations('categories'))
        ], 200);
    }

    /**
     * destroy a product
     */
    public function destroy(Product $product)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to delete this product!',
            ], 403);
        }

        $pictureUrl = $product->picture_url;

        $product->delete();

        // we delete file after database clean-up, in case the query failed
        // then we don't want to delete picture before it.

        if ($pictureUrl) {
            Storage::disk('public')->delete($pictureUrl);
        }

        return response()->json([
            'message' => 'product deleted successfully.'
        ], 200);
    }
}
