<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contract;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil proposals yang approved
        $proposals = Proposal::where('status', 'approved')->take(3)->get();

        // Ambil admin untuk approval
        $admin = User::role(['admin', 'pimpinan'])->first();

        if ($proposals->isEmpty()) {
            $this->command->warn('No approved proposals found. Creating sample contracts with any proposals...');
            $proposals = Proposal::take(3)->get();
        }

        foreach ($proposals as $index => $proposal) {
            $year = now()->year;
            $month = now()->format('m');
            $contractNumber = sprintf('LPPM/%s/%s/%04d', $year, $month, $index + 1);

            // Buat kontrak penelitian
            Contract::create([
                'contract_number' => $contractNumber,
                'proposal_id' => $proposal->id,
                'title' => 'Kontrak Penelitian: ' . $proposal->title,
                'type' => 'research',
                'description' => 'Kontrak penelitian untuk proposal "' . $proposal->title . '" dengan nilai kontrak sebesar Rp ' . number_format($proposal->fund_amount ?? 50000000, 0, ',', '.') . ' untuk periode ' . (12 + $index * 6) . ' bulan.',
                'contract_value' => $proposal->fund_amount ?? 50000000,
                'start_date' => Carbon::now()->addDays(7 + ($index * 30)),
                'end_date' => Carbon::now()->addDays(7 + ($index * 30))->addMonths(12 + ($index * 6)),
                'duration_months' => 12 + ($index * 6),

                // Pihak yang terlibat
                'first_party' => 'STAI Ali bin Abi Thalib Surabaya',
                'first_party_representative' => 'Dr. H. Ahmad Rektor, M.Pd',
                'first_party_position' => 'Rektor',
                'second_party_user_id' => $proposal->user_id,
                'second_party_position' => 'Dosen Tetap',

                // Status berdasarkan index
                'status' => match($index) {
                    0 => 'active',
                    1 => 'signed',
                    2 => 'pending_signature',
                    default => 'draft',
                },

                // Tanggal penandatanganan (untuk kontrak yang sudah ditandatangani)
                'signed_at' => $index <= 1 ? Carbon::now()->subDays(30 - ($index * 15)) : null,
                'signed_by_first_party' => $index <= 1 && $admin ? $admin->id : null,
                'second_party_signed_at' => $index <= 1 ? Carbon::now()->subDays(25 - ($index * 15)) : null,

                // Deliverables
                'deliverables' => [
                    [
                        'name' => 'Laporan Kemajuan Bulan 1-3',
                        'description' => 'Laporan progress penelitian untuk periode 3 bulan pertama',
                        'due_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                        'status' => $index == 0 ? 'completed' : 'pending',
                    ],
                    [
                        'name' => 'Laporan Kemajuan Bulan 4-6',
                        'description' => 'Laporan progress penelitian untuk periode bulan 4-6',
                        'due_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                        'status' => $index == 0 ? 'in_progress' : 'pending',
                    ],
                    [
                        'name' => 'Laporan Akhir Penelitian',
                        'description' => 'Laporan akhir penelitian yang komprehensif',
                        'due_date' => Carbon::now()->addMonths(12 + ($index * 6))->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Publikasi Ilmiah',
                        'description' => 'Artikel ilmiah yang dipublikasikan di jurnal terakreditasi',
                        'due_date' => Carbon::now()->addMonths(15 + ($index * 6))->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                ],

                // Jadwal pembayaran
                'payment_schedule' => [
                    [
                        'stage' => 'Tahap 1 - Awal',
                        'percentage' => 40,
                        'amount' => ($proposal->fund_amount ?? 50000000) * 0.4,
                        'due_date' => Carbon::now()->addDays(7 + ($index * 30))->format('Y-m-d'),
                        'status' => $index == 0 ? 'paid' : 'pending',
                    ],
                    [
                        'stage' => 'Tahap 2 - Progress',
                        'percentage' => 35,
                        'amount' => ($proposal->fund_amount ?? 50000000) * 0.35,
                        'due_date' => Carbon::now()->addMonths(6 + ($index * 3))->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                    [
                        'stage' => 'Tahap 3 - Akhir',
                        'percentage' => 25,
                        'amount' => ($proposal->fund_amount ?? 50000000) * 0.25,
                        'due_date' => Carbon::now()->addMonths(12 + ($index * 6))->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                ],

                // Syarat dan ketentuan
                'terms_and_conditions' => '<ol>
                    <li>Pihak kedua wajib melaksanakan penelitian sesuai dengan proposal yang telah disetujui oleh LPPM STAI Ali bin Abi Thalib Surabaya</li>
                    <li>Hasil penelitian menjadi hak milik STAI Ali bin Abi Thalib Surabaya dan dapat digunakan untuk kepentingan institusi</li>
                    <li>Pihak kedua wajib menyerahkan laporan kemajuan penelitian setiap 3 (tiga) bulan sekali</li>
                    <li>Pembayaran akan dilakukan sesuai dengan jadwal yang telah ditentukan dan berdasarkan pencapaian milestone</li>
                    <li>Kontrak dapat dibatalkan jika pihak kedua tidak memenuhi kewajiban yang telah ditetapkan</li>
                    <li>Pihak kedua wajib mempublikasikan hasil penelitian minimal dalam 1 (satu) artikel jurnal terakreditasi</li>
                    <li>Seluruh aktivitas penelitian harus mematuhi kode etik penelitian yang berlaku</li>
                    <li>Perubahan kontrak hanya dapat dilakukan dengan persetujuan tertulis dari kedua belah pihak</li>
                </ol>',

                'special_conditions' => $index == 0 ?
                    '<p>Penelitian ini memiliki tingkat prioritas tinggi dan diharapkan dapat memberikan kontribusi signifikan untuk pengembangan pendidikan agama Islam di Indonesia.</p>' :
                    null,

                'created_by' => $admin ? $admin->id : 1,
                'approved_by' => $index <= 1 && $admin ? $admin->id : null,
                'approved_at' => $index <= 1 ? Carbon::now()->subDays(35 - ($index * 15)) : null,
            ]);
        }

        // Buat contoh kontrak pengabdian
        if ($proposals->count() > 1) {
            $proposal = $proposals->get(1);
            $year = now()->year;
            $month = now()->format('m');
            $contractNumber = sprintf('LPPM/%s/%s/%04d', $year, $month, 4);

            Contract::create([
                'contract_number' => $contractNumber,
                'proposal_id' => $proposal->id,
                'title' => 'Kontrak Pengabdian Masyarakat: Program Pemberdayaan Digital Santri',
                'type' => 'service',
                'description' => 'Kontrak pengabdian masyarakat untuk program pemberdayaan digital santri di wilayah Surabaya Timur dengan fokus pada pelatihan teknologi informasi untuk meningkatkan kompetensi digital.',
                'contract_value' => 25000000,
                'start_date' => Carbon::now()->addDays(15),
                'end_date' => Carbon::now()->addDays(15)->addMonths(6),
                'duration_months' => 6,

                'first_party' => 'STAI Ali bin Abi Thalib Surabaya',
                'first_party_representative' => 'Dr. H. Ahmad Rektor, M.Pd',
                'first_party_position' => 'Rektor',
                'second_party_user_id' => $proposal->user_id,
                'second_party_position' => 'Dosen Tetap',

                'status' => 'draft',

                'deliverables' => [
                    [
                        'name' => 'Pelatihan Dasar Komputer',
                        'description' => 'Pelatihan dasar penggunaan komputer untuk 50 santri',
                        'due_date' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Workshop Internet dan Media Sosial',
                        'description' => 'Workshop penggunaan internet yang bijak dan media sosial',
                        'due_date' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Laporan Kegiatan Pengabdian',
                        'description' => 'Laporan lengkap kegiatan pengabdian masyarakat',
                        'due_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                ],

                'payment_schedule' => [
                    [
                        'stage' => 'Tahap 1 - Persiapan',
                        'percentage' => 30,
                        'amount' => 7500000,
                        'due_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                    [
                        'stage' => 'Tahap 2 - Pelaksanaan',
                        'percentage' => 50,
                        'amount' => 12500000,
                        'due_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                    [
                        'stage' => 'Tahap 3 - Evaluasi',
                        'percentage' => 20,
                        'amount' => 5000000,
                        'due_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                        'status' => 'pending',
                    ],
                ],

                'terms_and_conditions' => '<ol>
                    <li>Pihak kedua wajib melaksanakan kegiatan pengabdian masyarakat sesuai dengan rencana yang telah disetujui</li>
                    <li>Kegiatan pengabdian harus melibatkan partisipasi aktif masyarakat target</li>
                    <li>Pihak kedua wajib menyerahkan laporan kegiatan setiap bulan</li>
                    <li>Hasil kegiatan pengabdian harus didokumentasikan dengan baik</li>
                    <li>Publikasi hasil pengabdian dalam jurnal atau media publikasi lainnya sangat dianjurkan</li>
                </ol>',

                'created_by' => $admin ? $admin->id : 1,
            ]);
        }

        $this->command->info('Contract data seeded successfully!');
    }
}
