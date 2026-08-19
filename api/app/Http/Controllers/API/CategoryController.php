<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\SaveProductRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
