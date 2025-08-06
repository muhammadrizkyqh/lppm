<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResearchScheme;

class ResearchSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemes = [
            [
                'name' => 'Penelitian Dosen Pemula (PDP)',
                'description' => 'Skema penelitian untuk dosen dengan jabatan fungsional Asisten Ahli dan Lektor yang belum memiliki pengalaman penelitian yang memadai.',
                'type' => 'penelitian',
                'default_fund_amount' => 15000000,
                'registration_start_date' => now()->startOfYear(),
                'registration_end_date' => now()->startOfYear()->addMonths(2),
                'expected_outputs' => ['jurnal_sinta_4', 'jurnal_sinta_5', 'jurnal_sinta_6'],
                'is_active' => true,
            ],
            [
                'name' => 'Penelitian Dosen Unggulan (PDU)',
                'description' => 'Skema penelitian untuk dosen dengan track record penelitian yang baik dan berpotensi menghasilkan publikasi internasional.',
                'type' => 'penelitian',
                'default_fund_amount' => 25000000,
                'registration_start_date' => now()->startOfYear(),
                'registration_end_date' => now()->startOfYear()->addMonths(2),
                'expected_outputs' => ['jurnal_sinta_1', 'jurnal_sinta_2', 'jurnal_scopus'],
                'is_active' => true,
            ],
            [
                'name' => 'Pengabdian Kepada Masyarakat (PKM)',
                'description' => 'Skema pengabdian kepada masyarakat untuk meningkatkan kualitas hidup dan kesejahteraan masyarakat.',
                'type' => 'pengabdian',
                'default_fund_amount' => 12000000,
                'registration_start_date' => now()->startOfYear()->addMonths(3),
                'registration_end_date' => now()->startOfYear()->addMonths(5),
                'expected_outputs' => ['jurnal_sinta_4', 'jurnal_sinta_5', 'produk_inovasi'],
                'is_active' => true,
            ],
            [
                'name' => 'Penelitian Kolaboratif Internasional',
                'description' => 'Skema penelitian untuk kolaborasi dengan institusi luar negeri dengan target publikasi internasional.',
                'type' => 'penelitian',
                'default_fund_amount' => 50000000,
                'registration_start_date' => now()->startOfYear()->addMonths(6),
                'registration_end_date' => now()->startOfYear()->addMonths(8),
                'expected_outputs' => ['jurnal_scopus', 'jurnal_sinta_1'],
                'is_active' => false,
            ],
        ];

        foreach ($schemes as $scheme) {
            ResearchScheme::create($scheme);
        }
    }
}
