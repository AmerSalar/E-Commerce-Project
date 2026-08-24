<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\SaveCategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * get all categories
     */
    public function getAll(Request $request)
    {
        $include = $request->query('include');
        $perPage = $request->query('perPage', 10);

        $categories = $include === 'products' ?
            Category::with('products')->paginate($perPage) :
            Category::paginate($perPage);

        return new CategoryCollection($categories);
    }

    /**
     * get one category
     */
    public function getOne(Request $request, Category $category)
    {
        $include = $request->query('include');
        if ($include === 'products') {
            $category = $category->load('products');
        }
        return new CategoryResource($category);
    }

    /**
     * store a new category
     */
    public function store(SaveCategoryRequest $request)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to create a category!',
            ], 403);
        }

        $validated = $request->validated();

        $category = Category::create($validated);

        return response()->json([
            'message' => 'category created successfully.',
            'category' => new CategoryResource($category)
        ], 201);
    }

    /**
     * update a category
     */
    public function update(SaveCategoryRequest $request, Category $category)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to update this category!',
            ], 403);
        }
        $validated = $request->validated();

        $category->update($validated);

        return response()->json([
            'message' => 'category updated successfully.',
            'category' => new CategoryResource($category)
        ], 200);
    }

    /**
     * destroy a category
     */
    public function destroy(Category $category)
    {
        if (Gate::denies('manage')) {
            return response()->json([
                'message' => 'you are not authorized to delete this category!',
            ], 403);
        }
        $category->delete();

        return response()->json([
            'message' => 'category deleted successfully.'
        ], 200);
    }
}
