<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Software Engineer',
            'Project Manager',
            'Business Analyst',
            'Quality Assurance Engineer',
            'DevOps Engineer',
            'UI/UX Designer',
            'Database Administrator',
            'System Administrator',
            'Tech Lead',
            'CTO',
        ];

        foreach ($roles as $role) {
            Role::create(['role_name' => $role]);
        }
    }
}
