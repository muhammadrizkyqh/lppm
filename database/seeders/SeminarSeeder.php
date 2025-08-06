<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seminar;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;

class SeminarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil proposals yang sudah ada
        $proposals = Proposal::take(3)->get();

        // Ambil admin/pimpinan sebagai penjadwal
        $admin = User::role(['admin', 'pimpinan'])->first();

        if (!$admin) {
            $admin = User::first(); // fallback ke user pertama jika tidak ada admin
        }

        foreach ($proposals as $index => $proposal) {
            // Buat seminar internal
            Seminar::create([
                'proposal_id' => $proposal->id,
                'title' => 'Seminar Hasil Penelitian: ' . $proposal->title,
                'description' => 'Seminar presentasi hasil penelitian untuk proposal "' . $proposal->title . '" yang akan membahas temuan-temuan penting dan hasil yang telah dicapai.',
                'type' => $index % 2 == 0 ? 'internal' : 'public',
                'scheduled_at' => Carbon::now()->addDays(7 + ($index * 14))->setHour(13)->setMinute(0),
                'location' => $index % 2 == 0 ? 'Ruang Meeting LPPM' : 'Auditorium STAI Ali',
                'meeting_link' => $index == 1 ? 'https://zoom.us/j/123456789' : null,
                'status' => ['scheduled', 'completed'][$index % 2],
                'agenda' => '<ol><li>Pembukaan dan Perkenalan</li><li>Presentasi Hasil Penelitian oleh ' . $proposal->user->name . '</li><li>Diskusi dan Tanya Jawab</li><li>Masukan dan Saran dari Reviewer</li><li>Penutup</li></ol>',
                'notes' => $index % 2 == 1 ? '<p>Hasil seminar menunjukkan temuan yang signifikan dalam bidang ' . ($proposal->keywords ?? 'penelitian') . '. Penelitian telah mencapai target yang ditetapkan dengan beberapa rekomendasi untuk pengembangan lebih lanjut.</p>' : null,
                'feedback' => $index % 2 == 1 ? '<ul><li>Metodologi penelitian sudah tepat dan dapat dipertanggungjawabkan</li><li>Hasil penelitian memberikan kontribusi yang bermakna</li><li>Disarankan untuk mempublikasikan hasil di jurnal terakreditasi</li></ul>' : null,
                'scheduled_by' => $admin->id,
            ]);
        }

        // Buat beberapa seminar mendatang
        for ($i = 1; $i <= 2; $i++) {
            $proposal = $proposals->random();

            Seminar::create([
                'proposal_id' => $proposal->id,
                'title' => 'Seminar Progress Penelitian ' . $i . ': ' . $proposal->title,
                'description' => 'Seminar monitoring progress penelitian tahap ' . $i . ' untuk membahas pencapaian dan kendala yang dihadapi.',
                'type' => 'internal',
                'scheduled_at' => Carbon::now()->addDays(30 + ($i * 7))->setHour(10)->setMinute(0),
                'location' => 'Ruang Meeting LPPM',
                'status' => 'scheduled',
                'agenda' => '<ol><li>Review Progress Penelitian</li><li>Diskusi Kendala dan Solusi</li><li>Perencanaan Tahap Selanjutnya</li><li>Evaluasi dan Masukan</li></ol>',
                'scheduled_by' => $admin->id,
            ]);
        }

        $this->command->info('Seminar data seeded successfully!');
    }
}
