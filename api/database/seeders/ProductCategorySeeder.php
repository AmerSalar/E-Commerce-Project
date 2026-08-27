<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $categories = Category::all();

        $products->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(2, 3))->pluck('id')
            );
        });
    }
}
