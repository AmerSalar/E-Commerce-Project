<?php

namespace Tests\Feature\Product;

use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
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
    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $categories = Category::factory()->count(2)->create();
        $response = $this->postJson('/api/products', [
            'name' => "Minecraft",
            'description' => "Survival game",
            'price' => 9.99,
            'quantity' => 10,
            'category_ids' => $categories->pluck('id')->toArray(),
        ]);

        // status of 401
        $response->assertUnauthorized();
    }
    public function test_regular_user_cannot_create_product(): void
    {
        $categories = Category::factory()->count(2)->create();
        $user = User::factory()->create();

        // authenticates user via 'api' guard
        $response = $this->actingAs($user, 'api')
            ->postJson('/api/products', [
                'name' => "Minecraft",
                'description' => "Survival game",
                'price' => 9.99,
                'quantity' => 10,
                'category_ids' => $categories->pluck('id')->toArray(),
            ]);

        // status of 403
        $response->assertForbidden();
    }

    public function test_manager_can_store_product(): void
    {
        $categories = Category::factory()->count(2)->create();
        $role = Role::withoutTimestamps(
            fn() => Role::create(['name' => 'manager'])
        );
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/products', [
                'name' => "Minecraft",
                'description' => "Survival game",
                'price' => 9.99,
                'quantity' => 10,
                'category_ids' => $categories->pluck('id')->toArray(),
            ]);

        // 201
        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'product' => [
                    'id',
                    'name',
                    'price',
                    'quantity',
                ],
            ]);
        // check database for table "string" for data ['key'=>'value']
        $this->assertDatabaseHas(
            'products',
            [
                'name' => 'Minecraft',
                'price' => 9.99
            ]
        );
    }
}
