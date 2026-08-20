<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Factories\UserRoleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $roles = Role::all();

        $roles->each(function ($role) use ($users) {
            if ($role->id === 1) {
                return;
            }
            $role->users()->attach(
                $users->random(1)->pluck('id')
            );
        });
    }
}
