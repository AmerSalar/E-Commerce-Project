<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $include = $request->query('include');
        $perPage = $request->query('perPage', 10);

        $products = $include === 'categories' ?
            $products = Product::with('categories')->paginate($perPage) :
            $products = Product::paginate($perPage);

        return new ProductCollection($products);
    }
}
