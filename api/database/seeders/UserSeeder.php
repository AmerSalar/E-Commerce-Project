<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create owner
        $user = User::factory()->create([
            'name' => config('app.owner_name'),
            'email' => config('app.owner_email'),
            'password' => Hash::make(config('app.owner_password')),
        ]);
        $user->roles()->attach(1); // role id 1 = super admin
        Cart::factory()->create([
            'user_id' => $user->id
        ]);

        for ($i = 1; $i <= 20; $i++) {
            $user = User::factory()->create();
            Cart::factory()->create([
                'user_id' => $user->id
            ]);
        }
    }
}
