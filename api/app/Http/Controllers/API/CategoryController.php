<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\SaveCategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAll(Request $request)
    {
        $include = $request->query('include');
        $perPage = $request->query('perPage', 10);

        $categories = $include === 'products' ?
            Category::with('products')->paginate($perPage) :
            Category::paginate($perPage);

        return new CategoryCollection($categories);
    }

    public function getOne(Request $request, Category $category)
    {
        $include = $request->query('include');
        if ($include === 'products') {
            $category = $category->load('products');
        }
        return new CategoryResource($category);
    }

    public function store(SaveCategoryRequest $request)
    {
        $validated = $request->validated();

        $category = Category::create($validated);

        return response()->json([
            'message' => 'category created successfully.',
            'category' => new CategoryResource($category)
        ], 201);
    }

    public function update(SaveCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        $category->update($validated);

        return response()->json([
            'message' => 'category updated successfully.',
            'category' => new CategoryResource($category)
        ], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'category deleted successfully.'
        ], 200);
    }

    public static function notFound()
    {
        return response()->json([
            'message' => 'Category not found!'
        ], 404);
    }
}
