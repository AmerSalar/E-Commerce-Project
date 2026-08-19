<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use Database\Factories\UserRoleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carts = Cart::all();
        $items = Product::all();

        $carts->each(function ($cart) use ($items) {
            $cart->items()->attach(
                $items->random(rand(1, 3))->pluck('id')
            );
        });
    }
}
