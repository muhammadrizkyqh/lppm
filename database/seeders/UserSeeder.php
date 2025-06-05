<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin LPPM')->first();
        $dosenRole = Role::where('name', 'Dosen')->first();

        // Create Admin LPPM
        User::create([
            'name' => 'Admin LPPM',
            'email' => 'admin@lppm.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        // Create Sample Dosen
        User::create([
            'name' => 'Dr. John Doe',
            'email' => 'dosen@lppm.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $dosenRole->id,
            'email_verified_at' => now(),
        ]);
    }
}
