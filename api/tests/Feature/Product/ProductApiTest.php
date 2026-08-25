<?php

namespace Tests\Feature\Product;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_get_paginated_products(): void
    {
        Product::factory()->count(20)->create();

        $response = $this->getJson('/api/products?perPage=5');

        // check if status is 200
        $response->assertOk()
            // check if "data" has exactly 5 elements
            ->assertJsonCount(5, 'data')
            // check if json structure looks close to this,
            // and means at least have these..
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'quantity']
                ],
                'links',
                'meta'
            ]);
    }
    public function test_can_eager_load_category_relations(): void
    {
        $category = Category::factory()->create(['name' => 'disc']);
        $product = Product::factory()->create();
        $category->products()->attach($product);

        $response = $this->getJson('/api/products?include=categories');

        $response->assertOk()
            // it means at this path vvvvvvvv expect the 'disc'
            ->assertJsonPath('data.0.categories.0.name', 'disc');
    }
}
