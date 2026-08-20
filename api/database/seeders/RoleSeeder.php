<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    private $roles = [
        ['name' => 'super_admin'],
        ['name' => 'admin'],
        ['name' => 'manager'],
        ['name' => 'driver'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert($this->roles);
    }
}
