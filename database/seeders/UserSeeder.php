<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@lppm.ac.id'],
            [
                'name' => 'Administrator LPPM',
                'password' => Hash::make('password'),
                'nidn' => null,
                'phone' => '081234567890',
                'user_type' => 'admin',
                'functional_position' => null,
                'field_of_study' => 'Administrasi',
                'address' => 'Kampus STAI Ali',
                'employment_status' => 'tetap',
                'is_active' => true,
            ]
        );

        // Sample Dosen Tetap
        $dosens = [
            [
                'name' => 'Dr. Ahmad Maulana, S.Pd., M.Pd.',
                'email' => 'ahmad.maulana@stai-ali.ac.id',
                'nidn' => '0301088901',
                'phone' => '081234567891',
                'functional_position' => 'lektor',
                'field_of_study' => 'Pendidikan Islam',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Dr. Siti Khadijah, S.Ag., M.A.',
                'email' => 'siti.khadijah@stai-ali.ac.id',
                'nidn' => '0402078902',
                'phone' => '081234567892',
                'functional_position' => 'lektor_kepala',
                'field_of_study' => 'Studi Islam',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Prof. Dr. Muhammad Hidayat, M.Ag.',
                'email' => 'muhammad.hidayat@stai-ali.ac.id',
                'nidn' => '0503067801',
                'phone' => '081234567893',
                'functional_position' => 'guru_besar',
                'field_of_study' => 'Tafsir dan Hadits',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Dra. Fatimah Zahra, M.Pd.I.',
                'email' => 'fatimah.zahra@stai-ali.ac.id',
                'nidn' => '0604058703',
                'phone' => '081234567894',
                'functional_position' => 'asisten_ahli',
                'field_of_study' => 'Pendidikan Agama Islam',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Ustadz Ali Rahman, S.Th.I., M.A.',
                'email' => 'ali.rahman@stai-ali.ac.id',
                'nidn' => '0705099004',
                'phone' => '081234567895',
                'functional_position' => 'asisten_ahli',
                'field_of_study' => 'Fiqh dan Ushul Fiqh',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Dr. Kholilah Sari, S.Pd., M.Pd.',
                'email' => 'kholilah.sari@stai-ali.ac.id',
                'nidn' => '0806088605',
                'phone' => '081234567896',
                'functional_position' => 'lektor',
                'field_of_study' => 'Manajemen Pendidikan',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Ustadz Ibrahim Khalil, S.Ag., M.A.',
                'email' => 'ibrahim.khalil@stai-ali.ac.id',
                'nidn' => '0907077506',
                'phone' => '081234567897',
                'functional_position' => 'lektor',
                'field_of_study' => 'Bahasa Arab',
                'employment_status' => 'tetap',
            ],
            [
                'name' => 'Dra. Aisyah Nur, M.Pd.',
                'email' => 'aisyah.nur@stai-ali.ac.id',
                'nidn' => '1008068907',
                'phone' => '081234567898',
                'functional_position' => 'asisten_ahli',
                'field_of_study' => 'Psikologi Pendidikan',
                'employment_status' => 'tetap',
            ],
        ];

        foreach ($dosens as $dosen) {
            User::updateOrCreate(
                ['email' => $dosen['email']],
                array_merge($dosen, [
                    'password' => Hash::make('password'),
                    'user_type' => 'dosen',
                    'address' => 'Alamat ' . $dosen['name'],
                    'is_active' => true,
                ])
            );
        }

        // Sample Reviewer
        User::updateOrCreate(
            ['email' => 'reviewer@lppm.ac.id'],
            [
                'name' => 'Dr. Reviewer Ahli, M.Pd.',
                'password' => Hash::make('password'),
                'nidn' => '1109067808',
                'phone' => '081234567899',
                'user_type' => 'reviewer',
                'functional_position' => 'lektor',
                'field_of_study' => 'Evaluasi Penelitian',
                'address' => 'Alamat Reviewer',
                'employment_status' => 'tetap',
                'is_active' => true,
            ]
        );

        // Sample Pimpinan
        User::updateOrCreate(
            ['email' => 'pimpinan@lppm.ac.id'],
            [
                'name' => 'Prof. Dr. Pimpinan LPPM, M.A.',
                'password' => Hash::make('password'),
                'nidn' => '1210057009',
                'phone' => '081234567800',
                'user_type' => 'pimpinan',
                'functional_position' => 'guru_besar',
                'field_of_study' => 'Manajemen Penelitian',
                'address' => 'Alamat Pimpinan',
                'employment_status' => 'tetap',
                'is_active' => true,
            ]
        );
    }
}
