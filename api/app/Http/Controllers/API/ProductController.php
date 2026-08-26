<?php

namespace App\Http\Controllers\API;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\SaveProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
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

        return new ProductCollection($products);
    }
    /**
     * get one product
     */
    public function show(Request $request, Product $product)
    {
        return new ProductResource(
            $product->withRelations($request->query('include'))
        );
    }
    /**
     * store a new product
     */
    public function store(SaveProductRequest $request)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to create a product!',
            ], 403);
        }

        $validated = $request->validated();

        $picture = $request->file('picture');
        $generatedFileName = null;
        if ($picture) {
            $generatedFileName = 'products/' . Str::uuid() . '.webp';

            $encodedImage = HelperFunctions::compressImage($picture);
            Storage::disk('public')->put($generatedFileName, (string) $encodedImage);

            $validated['picture_url'] = $generatedFileName;
            unset($validated['picture']);
        }

        try {
            // transaction means do all, if one of them fails, everyone fail too
            $product = DB::transaction(function () use ($validated) {

                $product = Product::create($validated);
                // category_ids is a required field
                $product->categories()->attach($validated['category_ids']);

                return $product;
            });
        } catch (\Throwable $e) {
            if ($generatedFileName) {
                Storage::disk('public')->delete($generatedFileName);
            }
            return response()->json([
                'message' => 'Failed to store product!',
            ], 422);
        }

        return response()->json([
            'message' => 'product created successfully.',
            'product' => new ProductResource($product->load('categories'))
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
        $oldPictureUrl = $product->picture_url;
        $generatedFileName = null;

        if ($picture) {
            $generatedFileName = 'products/' . Str::uuid() . '.webp';

            $encodedImage = HelperFunctions::compressImage($picture);
            Storage::disk('public')->put($generatedFileName, (string) $encodedImage);
            $validated['picture_url'] = $generatedFileName;
            unset($validated['picture']);
        }

        try {
            DB::transaction(function () use ($product, $request, $validated) {

                $product->update($validated);
                $product->categories()->sync(
                    $validated['category_ids'] ?? []
                );
            });
        } catch (\Throwable $th) {
            if ($generatedFileName) {
                Storage::disk('public')->delete($generatedFileName);
            }
            return response()->json([
                'message' => 'failed to update product!',
            ], 422);
        }

        if ($generatedFileName && $oldPictureUrl) {
            Storage::disk('public')->delete($oldPictureUrl);
        }

        return response()->json([
            'message' => 'product updated successfully.',
            'product' => new ProductResource($product->load('categories'))
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
