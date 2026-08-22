<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\SaveProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $include = $request->query('include');
        $perPage = $request->query('perPage', 10);

        $products = $include === 'categories' ?
            Product::with('categories')->paginate($perPage) :
            Product::paginate($perPage);

        return new ProductCollection($products);
    }
    public function getSingleProduct(Request $request, Product $product)
    {
        $include = $request->query('include');
        if ($include === 'categories') {
            $product = $product->load('categories');
        }
        return new ProductResource($product);
    }
    public function storeProduct(SaveProductRequest $request)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to create a product!',
            ], 403);
        }

        $validated = $request->validated();

        // transaction means do all, if one of them fails, everyone fail too

        $product = DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            if (!empty($validated['category_ids'])) {
                $product->categories()->attach($validated['category_ids']);
            }

            return $product;
        });

        return response()->json([
            'message' => 'product created successfully.',
            'product' => new ProductResource($product->load('categories'))
        ], 201);
    }
    public function updateProduct(SaveProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->update($validated);

        DB::transaction(function () use ($product, $request, $validated) {
            $product->update($validated);

            if ($request->has('category_ids')) {
                $product->categories()->sync(
                    $validated['category_ids'] ?? []
                );
            }
        });

        return response()->json([
            'message' => 'product updated successfully.',
            'product' => new ProductResource($product->load('categories'))
        ], 200);
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'product deleted successfully.'
        ], 200);
    }
    public static function productNotFound()
    {
        return response()->json([
            'message' => 'Product not found!'
        ], 404);
    }
}
