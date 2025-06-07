<?php

namespace Database\Seeders;

use App\Models\KategoriPenelitian;
use Illuminate\Database\Seeder;

class KategoriPenelitianSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama_kategori' => 'Dosen Pemula',
                'deskripsi' => 'Penelitian untuk dosen pemula dengan dana maksimal Rp 50.000.000 dan durasi maksimal 12 bulan. Diperuntukkan bagi dosen yang belum pernah mendapat hibah penelitian.'
            ],
            [
                'nama_kategori' => 'Penelitian Unggulan',
                'deskripsi' => 'Penelitian unggulan dengan dana maksimal Rp 100.000.000 dan durasi maksimal 24 bulan. Untuk penelitian yang memiliki keunggulan dan inovasi tertentu.'
            ],
            [
                'nama_kategori' => 'Hibah Penelitian Internal',
                'deskripsi' => 'Hibah penelitian internal STAI dengan dana maksimal Rp 25.000.000 dan durasi maksimal 8 bulan. Untuk penelitian dasar dan terapan.'
            ],
            [
                'nama_kategori' => 'Penelitian Kolaboratif',
                'deskripsi' => 'Penelitian yang melibatkan kolaborasi antar dosen atau dengan institusi lain. Dana maksimal Rp 75.000.000 dengan durasi 18 bulan.'
            ],
            [
                'nama_kategori' => 'Penelitian Berbasis Syariah',
                'deskripsi' => 'Penelitian yang fokus pada kajian keislaman dan syariah. Dana maksimal Rp 40.000.000 dengan durasi 12 bulan.'
            ],
            [
                'nama_kategori' => 'Penelitian Teknologi',
                'deskripsi' => 'Penelitian di bidang teknologi dan digitalisasi untuk mendukung transformasi digital. Dana maksimal Rp 60.000.000.'
            ],
            [
                'nama_kategori' => 'Penelitian Sosial Kemasyarakatan',
                'deskripsi' => 'Penelitian yang berfokus pada isu-isu sosial dan kemasyarakatan. Dana maksimal Rp 35.000.000 dengan durasi 10 bulan.'
            ],
            [
                'nama_kategori' => 'Penelitian Multidisiplin',
                'deskripsi' => 'Penelitian yang menggabungkan beberapa disiplin ilmu. Dana maksimal Rp 80.000.000 dengan durasi maksimal 20 bulan.'
            ]
        ];

        foreach ($kategoris as $kategori) {
            KategoriPenelitian::create($kategori);
        }
    }
}
