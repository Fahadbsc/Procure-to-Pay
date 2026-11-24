<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin',
            'Requester',
            'Dept Head',
            'IT Head',
            'Finance Head',
            'CFO',
            'Procurement Officer',
            'Store Keeper',
            'Accounts',
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role]
            );
        }
    }
}
