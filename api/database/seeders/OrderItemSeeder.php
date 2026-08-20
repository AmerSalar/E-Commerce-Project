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
            $randomProducts = $items->random(rand(1, 3));

            $pivotTableData = [];

            foreach ($randomProducts as $product) {
                $pivotTableData[$product->id] = [
                    // this is for a snapshot, maybe if product was changed in future, this stays
                    'item_name' => $product->name,
                    'item_price' => $product->price,
                    'quantity' => rand(1, 3),
                ];
            }

            $order->items()->attach($pivotTableData);
        });
    }
}
