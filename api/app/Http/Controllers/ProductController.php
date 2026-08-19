<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\SaveProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

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
        if (!$product) {
            return response()->json(['message' => 'not found']);
        }
        $include = $request->query('include');
        if ($include === 'categories') {
            $product = $product->load('categories');
        }
        return new ProductResource($product);
    }
    public function storeProduct(SaveProductRequest $request)
    {
        $attributes = $request->validated();

        $product = Product::create($attributes);
        $product->categories()->attach($request->input('category_ids'));

        return new ProductResource($product);
    }
}
