<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin LPPM',
                'description' => 'Administrator LPPM dengan akses penuh ke sistem'
            ],
            [
                'name' => 'Dosen',
                'description' => 'Dosen yang dapat mengajukan penelitian dan pengabdian'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
