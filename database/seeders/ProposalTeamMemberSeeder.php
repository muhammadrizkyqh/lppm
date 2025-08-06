<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProposalTeamMember;
use App\Models\Proposal;
use App\Models\User;

class ProposalTeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa proposals yang ada
        $proposals = Proposal::take(3)->get();

        // Ambil users yang memiliki role dosen (menggunakan Spatie Laravel Permission)
        $dosens = User::role('dosen')->take(6)->get();

        if ($dosens->count() < 2) {
            $this->command->warn('Not enough dosen users found. Creating additional dosen users...');

            // Buat dosen tambahan jika diperlukan
            for ($i = $dosens->count(); $i < 6; $i++) {
                $dosen = User::create([
                    'name' => 'Dr. Dosen ' . ($i + 1),
                    'email' => 'dosen' . ($i + 1) . '@stai-ali.ac.id',
                    'password' => bcrypt('password123'),
                    'nidn' => '000' . ($i + 1) . '12345' . ($i + 1),
                    'phone' => '08123456789' . $i,
                    'user_type' => 'dosen',
                    'functional_position' => ['asisten_ahli', 'lektor', 'lektor_kepala'][array_rand(['asisten_ahli', 'lektor', 'lektor_kepala'])],
                    'field_of_study' => 'Pendidikan Agama Islam',
                    'employment_status' => 'tetap',
                    'is_active' => true,
                ]);
                $dosen->assignRole('dosen');
                $dosens->push($dosen);
            }
        }

        foreach ($proposals as $index => $proposal) {
            // Tambah ketua tim (dosen)
            if (isset($dosens[$index * 2])) {
                $ketua = $dosens[$index * 2];
                ProposalTeamMember::create([
                    'proposal_id' => $proposal->id,
                    'user_id' => $ketua->id,
                    'member_type' => 'dosen',
                    'role' => 'ketua',
                    'name' => $ketua->name,
                    'nidn' => $ketua->nidn ?? '1234567890',
                    'position' => $ketua->functional_position ?? 'lektor',
                    'department' => 'Fakultas Agama Islam',
                    'expertise' => $ketua->field_of_study ?? 'Pendidikan Agama Islam',
                    'phone' => $ketua->phone ?? '081234567890',
                    'email' => $ketua->email,
                ]);
            }

            // Tambah 1-2 anggota tim
            $memberCount = rand(1, 2);
            for ($i = 1; $i <= $memberCount; $i++) {
                if (isset($dosens[$index * 2 + $i])) {
                    $anggota = $dosens[$index * 2 + $i];
                    ProposalTeamMember::create([
                        'proposal_id' => $proposal->id,
                        'user_id' => $anggota->id,
                        'member_type' => 'dosen',
                        'role' => 'anggota',
                        'name' => $anggota->name,
                        'nidn' => $anggota->nidn ?? '1234567890',
                        'position' => $anggota->functional_position ?? 'asisten_ahli',
                        'department' => 'Fakultas Agama Islam',
                        'expertise' => $anggota->field_of_study ?? 'Pendidikan Agama Islam',
                        'phone' => $anggota->phone ?? '081234567890',
                        'email' => $anggota->email,
                    ]);
                }
            }

            // Kadang tambah mahasiswa sebagai anggota
            if (rand(1, 10) > 7) { // 30% chance
                ProposalTeamMember::create([
                    'proposal_id' => $proposal->id,
                    'user_id' => null,
                    'member_type' => 'mahasiswa',
                    'role' => 'anggota',
                    'name' => 'Ahmad Mahasiswa ' . ($index + 1),
                    'nim' => '2021040' . ($index + 1) . '001',
                    'study_program' => 'Pendidikan Agama Islam',
                    'semester' => rand(4, 8),
                    'phone' => '081234567' . ($index + 1) . '00',
                    'email' => 'mahasiswa' . ($index + 1) . '@student.stai-ali.ac.id',
                ]);
            }
        }

        $this->command->info('ProposalTeamMember data seeded successfully!');
    }
}
