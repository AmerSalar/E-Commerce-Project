<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected $products = [
        [
            'name'        => 'PS5 Disc Edition',
            'description' => '4K gaming console.',
            'price'       => 499.99,
            'quantity'    => 15,
        ],
        [
            'name'        => 'Xbox Series X',
            'description' => '4K 1TB console.',
            'price'       => 499.99,
            'quantity'    => 12,
        ],
        [
            'name'        => 'PS4 Slim 1TB',
            'description' => 'Not provided',
            'price'       => 249.99,
            'quantity'    => 6,
        ],
        [
            'name'        => 'Xbox Series S',
            'description' => 'All-digital console.',
            'price'       => 299.99,
            'quantity'    => 14,
        ],
        [
            'name'        => 'God of War Ragnarök PS5',
            'description' => 'Action RPG disc.',
            'price'       => 69.99,
            'quantity'    => 25,
        ],
        [
            'name'        => 'Spider-Man 2 PS5',
            'description' => 'Action adventure disc.',
            'price'       => 69.99,
            'quantity'    => 30,
        ],
        [
            'name'        => 'The Witcher 3 PS5',
            'description' => 'Complete edition disc.',
            'price'       => 39.99,
            'quantity'    => 18,
        ],
        [
            'name'        => 'Cyberpunk 2077 PS4',
            'description' => 'Not provided',
            'price'       => 29.99,
            'quantity'    => 22,
        ],
        [
            'name'        => 'Elden Ring Xbox',
            'description' => 'Open-world RPG disc.',
            'price'       => 59.99,
            'quantity'    => 20,
        ],
        [
            'name'        => 'Kingston 16GB DDR4',
            'description' => 'Not provided',
            'price'       => 44.99,
            'quantity'    => 45,
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([...$this->products, 'created_at' => now(), 'updated_at' => now()]);
    }
}
