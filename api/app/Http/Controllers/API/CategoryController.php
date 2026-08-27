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
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 10);

        $categories = Category::query()
            ->withRelations($request->query('include'))
            ->paginate($perPage);

        return new CategoryCollection($categories);
    }

    /**
     * get one category
     */
    public function show(Request $request, Category $category)
    {
        $category = $category->loadRelations($request->query('include'));

        return new CategoryResource($category);
    }

    /**
     * store a new category
     */
    public function store(SaveCategoryRequest $request)
    {
        Gate::authorize('manage');

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
        Gate::authorize('manage');

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
        Gate::authorize('manage');
        $category->delete();

        return response()->json([
            'message' => 'category deleted successfully.'
        ], 200);
    }
}
