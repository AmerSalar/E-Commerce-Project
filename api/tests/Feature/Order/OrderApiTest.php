<?php

namespace Tests\Feature\Order;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;
    public function test_order_fails_if_product_destroyed_while_in_cart(): void
    {
        $customer = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $customer->id]);
        $product = Product::factory()->create([
            'name' => "Minecraft",
            'price' => 9.99,
            'quantity' => 10
        ]);

        $cart->items()->attach($product->id, [
            'quantity' => 1
        ]);

        $product->delete();

        // check if nullOnDelete constraint works for product_id foreign key
        $this->assertDatabaseHas(
            'cart_items',
            [
                'cart_id' => $cart->id,
                'product_id' => null
            ]
        );

        $response = $this->actingAs($customer, 'api')
            ->postJson('/api/orders/order-now', [
                'phone' => "07501234567",
                'city' => "slemani",
                'street' => "Park st.",
                'building' => 18,
            ]);

        $response->assertUnprocessable();
    }
}
