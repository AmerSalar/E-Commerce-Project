<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private $categories = [
        ['name' => 'console'],
        ['name' => 'disc'],
        ['name' => 'pc'],
        ['name' => 'ps4'],
        ['name' => 'ps5'],
        ['name' => 'xbox-series-x'],
        ['name' => 'xbox-series-s'],
        ['name' => 'xbox-one'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert($this->categories);
    }
}
