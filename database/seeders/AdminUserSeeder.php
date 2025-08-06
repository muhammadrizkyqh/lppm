<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::create([
            'name' => 'Admin LPPM',
            'email' => 'admin@lppm-stai-ali.ac.id',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        // Create sample dosen user
        $dosen = User::create([
            'name' => 'Dr. Ahmad Fulan',
            'email' => 'ahmad.fulan@stai-ali.ac.id',
            'password' => Hash::make('password123'),
            'nidn' => '0123456789',
            'phone' => '081234567890',
            'user_type' => 'dosen',
            'functional_position' => 'lektor',
            'field_of_study' => 'Pendidikan Agama Islam',
            'employment_status' => 'tetap',
            'is_active' => true,
        ]);

        $dosen->assignRole('dosen');

        // Create sample reviewer user
        $reviewer = User::create([
            'name' => 'Prof. Dr. Siti Reviewer',
            'email' => 'reviewer@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'reviewer',
            'is_active' => true,
        ]);

        $reviewer->assignRole('reviewer');

        // Create sample pimpinan user
        $pimpinan = User::create([
            'name' => 'Dr. Rektor STAI Ali',
            'email' => 'pimpinan@stai-ali.ac.id',
            'password' => Hash::make('password123'),
            'user_type' => 'pimpinan',
            'is_active' => true,
        ]);

        $pimpinan->assignRole('pimpinan');
    }
}
