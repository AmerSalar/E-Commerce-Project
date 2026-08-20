<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => config('app.owner_name'),
            'email' => config('app.owner_email'),
            'password' => Hash::make(config('app.owner_password')),
        ]);


        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductCategorySeeder::class,
            CartSeeder::class,
            CartItemSeeder::class,
            AddressSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);

        $user->roles()->attach(1); // role id 1 = super admin
    }
}
