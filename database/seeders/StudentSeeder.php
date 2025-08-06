<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            // Mahasiswa S1
            [
                'name' => 'Muhammad Ridwan',
                'nim' => '21010101',
                'email' => 'muhammad.ridwan@student.stai-ali.ac.id',
                'phone' => '081234560001',
                'education_level' => 'S1',
                'study_program' => 'Pendidikan Agama Islam',
                'department' => 'Fakultas Tarbiyah',
                'semester' => 6,
                'entry_year' => 2021,
                'gpa' => 3.45,
                'address' => 'Jl. Pendidikan No. 1, Jakarta',
            ],
            [
                'name' => 'Siti Aminah',
                'nim' => '21010102',
                'email' => 'siti.aminah@student.stai-ali.ac.id',
                'phone' => '081234560002',
                'education_level' => 'S1',
                'study_program' => 'Pendidikan Agama Islam',
                'department' => 'Fakultas Tarbiyah',
                'semester' => 6,
                'entry_year' => 2021,
                'gpa' => 3.78,
                'address' => 'Jl. Masjid No. 15, Bogor',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'nim' => '22010201',
                'email' => 'ahmad.fauzi@student.stai-ali.ac.id',
                'phone' => '081234560003',
                'education_level' => 'S1',
                'study_program' => 'Studi Islam',
                'department' => 'Fakultas Ushuluddin',
                'semester' => 4,
                'entry_year' => 2022,
                'gpa' => 3.23,
                'address' => 'Jl. Tafsir No. 8, Depok',
            ],
            [
                'name' => 'Fatimah Sari',
                'nim' => '22010202',
                'email' => 'fatimah.sari@student.stai-ali.ac.id',
                'phone' => '081234560004',
                'education_level' => 'S1',
                'study_program' => 'Studi Islam',
                'department' => 'Fakultas Ushuluddin',
                'semester' => 4,
                'entry_year' => 2022,
                'gpa' => 3.89,
                'address' => 'Jl. Hadits No. 12, Tangerang',
            ],
            [
                'name' => 'Ali Imran',
                'nim' => '23010301',
                'email' => 'ali.imran@student.stai-ali.ac.id',
                'phone' => '081234560005',
                'education_level' => 'S1',
                'study_program' => 'Hukum Islam',
                'department' => 'Fakultas Syariah',
                'semester' => 2,
                'entry_year' => 2023,
                'gpa' => 3.56,
                'address' => 'Jl. Fiqh No. 20, Bekasi',
            ],

            // Mahasiswa S2
            [
                'name' => 'Dr. Candidate Hasan',
                'nim' => 'S2210101',
                'email' => 'hasan.candidate@student.stai-ali.ac.id',
                'phone' => '081234560006',
                'education_level' => 'S2',
                'study_program' => 'Magister Pendidikan Islam',
                'department' => 'Program Pascasarjana',
                'semester' => 3,
                'entry_year' => 2022,
                'gpa' => 3.67,
                'address' => 'Jl. Magister No. 5, Jakarta Selatan',
            ],
            [
                'name' => 'Khadijah Master',
                'nim' => 'S2210102',
                'email' => 'khadijah.master@student.stai-ali.ac.id',
                'phone' => '081234560007',
                'education_level' => 'S2',
                'study_program' => 'Magister Studi Islam',
                'department' => 'Program Pascasarjana',
                'semester' => 3,
                'entry_year' => 2022,
                'gpa' => 3.84,
                'address' => 'Jl. Pascasarjana No. 10, Bandung',
            ],
            [
                'name' => 'Ibrahim Researcher',
                'nim' => 'S2230201',
                'email' => 'ibrahim.researcher@student.stai-ali.ac.id',
                'phone' => '081234560008',
                'education_level' => 'S2',
                'study_program' => 'Magister Manajemen Pendidikan Islam',
                'department' => 'Program Pascasarjana',
                'semester' => 1,
                'entry_year' => 2023,
                'gpa' => 3.72,
                'address' => 'Jl. Penelitian No. 25, Yogyakarta',
            ],

            // Mahasiswa S3
            [
                'name' => 'Future Dr. Abdullah',
                'nim' => 'S3220101',
                'email' => 'abdullah.phd@student.stai-ali.ac.id',
                'phone' => '081234560009',
                'education_level' => 'S3',
                'study_program' => 'Doktor Pendidikan Islam',
                'department' => 'Program Doktoral',
                'semester' => 4,
                'entry_year' => 2022,
                'gpa' => 3.91,
                'address' => 'Jl. Doktoral No. 7, Surabaya',
            ],
            [
                'name' => 'Future Dr. Maryam',
                'nim' => 'S3230201',
                'email' => 'maryam.phd@student.stai-ali.ac.id',
                'phone' => '081234560010',
                'education_level' => 'S3',
                'study_program' => 'Doktor Studi Islam',
                'department' => 'Program Doktoral',
                'semester' => 2,
                'entry_year' => 2023,
                'gpa' => 3.88,
                'address' => 'Jl. Disertasi No. 18, Semarang',
            ],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['nim' => $student['nim']],
                array_merge($student, [
                    'is_active' => true,
                ])
            );
        }
    }
}
