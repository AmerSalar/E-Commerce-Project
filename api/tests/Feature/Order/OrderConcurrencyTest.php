<?php

namespace Tests\Feature\Order;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Spatie\Fork\Fork;
use Tests\TestCase;

class OrderConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        if (PHP_OS_FAMILY === 'Windows' || ! extension_loaded('pcntl')) {
            $this->markTestSkipped('Fork tests require Linux/Docker with pcntl.');
        }
    }
    public function test_second_order_fails_when_first_takes_stock(): void
    {
        $product = Product::factory()->create([
            'name' => "Minecraft",
            'price' => 9.99,
            'quantity' => 1
        ]);

        $customerA = User::factory()->create();
        $cartA = Cart::factory()->create(['user_id' => $customerA->id]);
        $cartA->items()->attach($product->id, ['quantity' => 1]);

        $customerB = User::factory()->create();
        $cartB = Cart::factory()->create(['user_id' => $customerB->id]);
        $cartB->items()->attach($product->id, ['quantity' => 1]);

        $payload = [
            'phone' => "07509876543",
            'city' => "Slemani",
            'street' => "Park",
            'building' => 10
        ];

        $statuses = Fork::new()
            ->before(function () {
                $this->refreshApplication();
                DB::purge();
                DB::reconnect();
            })
            ->run(
                function () use ($customerA, $payload) {
                    return $this->actingAs($customerA, 'api')
                        ->postJson("/api/orders/order-now", $payload)
                        ->status();
                },
                function () use ($customerB, $payload) {
                    return $this->actingAs($customerB, 'api')
                        ->postJson("/api/orders/order-now", $payload)
                        ->status();
                }
            );

        $this->assertCount(2, $statuses);
        $this->assertContains(201, $statuses);
        $this->assertContains(422, $statuses);
        $this->assertEquals(0, $product->fresh()->quantity);
    }
}
