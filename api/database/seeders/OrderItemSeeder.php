<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Database\Factories\UserRoleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $items = Product::all();

        $orders->each(function ($order) use ($items) {
            $order->items()->attach(
                $items->random(rand(1, 3))->pluck('id')
            );
        });
    }
}
