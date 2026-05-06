<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'admin'],
            ['role_name' => 'team_lead'],
            ['role_name' => 'developer'],
            ['role_name' => 'applicant'],
            ['role_name' => 'project_coordinator'],
            ['role_name' => 'department']
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
