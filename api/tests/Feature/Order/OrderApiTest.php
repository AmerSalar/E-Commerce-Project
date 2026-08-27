<?php

namespace Tests\Feature\Order;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\QueryException;
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

    public function test_product_lock_prevents_concurrent_reads(): void
    {
        $product = Product::factory()->create([
            'name' => "Minecraft",
            'price' => 9.99,
            'quantity' => 1
        ]);

        config(['database.connections.mysql_second' => config('database.connections.mysql')]);
        $connectionA = DB::connection('mysql');
        $connectionB = DB::connection('mysql_second');

        // this gives the lock-wait a short timeout so it happens fast, and wont wait
        $connectionB->statement("SET SESSION innodb_lock_wait_timeout = 1");

        // we make first person start transaction
        // and then lock the product
        $connectionA->beginTransaction();
        $connectionA->table('products')->where('id', $product->id)
            ->lockForUpdate()
            ->first();

        // then second person's transaction begins
        $connectionB->beginTransaction();

        // now second person wants to lock the product too
        try {
            $connectionB->table('products')->where('id', $product->id)
                ->lockForUpdate()
                ->first();

            $this->fail("Second connection failed to lock the product unexpectedly!");
        } catch (QueryException $e) {
            // if second lock failed, it will throw a query exception
            // we check if it threw the exception, then it means the test succeeded.
            $this->assertStringContainsString(
                "Lock wait timeout exceeded",
                $e->getMessage()
            );
        } finally {
            // these to close the 2 transactions we begun.
            $connectionA->rollBack();
            $connectionB->rollBack();
        }
    }
}
