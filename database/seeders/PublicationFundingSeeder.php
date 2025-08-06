<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PublicationFunding;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;

class PublicationFundingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil proposals yang approved
        $proposals = Proposal::where('status', 'approved')->take(3)->get();

        // Ambil admin untuk reviewer
        $admin = User::role(['admin', 'pimpinan'])->first();

        if ($proposals->isEmpty()) {
            $this->command->warn('No approved proposals found. Creating sample publication fundings with any proposals...');
            $proposals = Proposal::take(3)->get();
        }

        foreach ($proposals as $index => $proposal) {
            // Buat pengajuan jurnal internasional
            PublicationFunding::create([
                'proposal_id' => $proposal->id,
                'user_id' => $proposal->user_id,
                'title' => 'Digital Learning Model for Islamic Education: A Comprehensive Study in Indonesian Higher Education',
                'publication_type' => 'journal_international',
                'journal_name' => 'International Journal of Islamic Education',
                'publisher' => null,
                'abstract' => '<p>This research investigates the effectiveness of digital learning models in Islamic education within Indonesian higher education institutions. The study employs a mixed-method approach combining quantitative analysis of student performance and qualitative assessment of learning experiences. Results demonstrate significant improvements in student engagement and learning outcomes when digital technologies are integrated with traditional Islamic pedagogical methods.</p>',
                'estimated_cost' => 8000000,
                'cost_breakdown' => '<ul><li>Journal publication fee: Rp 5.000.000</li><li>Professional translation: Rp 1.500.000</li><li>Proofreading service: Rp 1.000.000</li><li>Administrative costs: Rp 500.000</li></ul>',
                'submission_deadline' => Carbon::now()->addDays(30),
                'expected_publication_date' => Carbon::now()->addMonths(6),
                'status' => $index == 0 ? 'approved' : 'under_review',
                'reviewer_notes' => $index == 0 ? '<p>Penelitian menunjukkan metodologi yang solid dan hasil yang signifikan. Direkomendasikan untuk publikasi di jurnal internasional bereputasi.</p>' : null,
                'approved_amount' => $index == 0 ? 7500000 : null,
                'reviewed_by' => $index == 0 && $admin ? $admin->id : null,
                'reviewed_at' => $index == 0 ? Carbon::now()->subDays(5) : null,
                'submitted_at' => Carbon::now()->subDays(10),
            ]);

            // Buat pengajuan jurnal nasional
            if ($index < 2) {
                PublicationFunding::create([
                    'proposal_id' => $proposal->id,
                    'user_id' => $proposal->user_id,
                    'title' => 'Model Pembelajaran Digital dalam Pendidikan Agama Islam di Era Post-Pandemic',
                    'publication_type' => 'journal_national',
                    'journal_name' => 'Jurnal Pendidikan Agama Islam',
                    'publisher' => null,
                    'abstract' => '<p>Penelitian ini mengkaji implementasi model pembelajaran digital dalam pendidikan agama Islam di era post-pandemic. Dengan menggunakan pendekatan kualitatif, penelitian ini menganalisis tantangan dan peluang dalam mengintegrasikan teknologi digital dengan nilai-nilai pendidikan Islam tradisional.</p>',
                    'estimated_cost' => 3500000,
                    'cost_breakdown' => '<ul><li>Biaya publikasi jurnal: Rp 2.000.000</li><li>Proofreading: Rp 800.000</li><li>Biaya administrasi: Rp 700.000</li></ul>',
                    'submission_deadline' => Carbon::now()->addDays(45),
                    'expected_publication_date' => Carbon::now()->addMonths(4),
                    'status' => 'submitted',
                    'submitted_at' => Carbon::now()->subDays(5),
                ]);
            }

            // Buat pengajuan konferensi internasional
            if ($index == 1) {
                PublicationFunding::create([
                    'proposal_id' => $proposal->id,
                    'user_id' => $proposal->user_id,
                    'title' => 'Innovation in Islamic Higher Education: Technology Integration Strategies',
                    'publication_type' => 'conference_international',
                    'journal_name' => 'International Conference on Islamic Education and Technology (ICIET 2025)',
                    'publisher' => null,
                    'abstract' => '<p>This paper presents innovative strategies for integrating technology in Islamic higher education institutions. The research provides practical frameworks and implementation guidelines for educators and administrators seeking to modernize Islamic education while preserving its core values and principles.</p>',
                    'estimated_cost' => 12000000,
                    'cost_breakdown' => '<ul><li>Conference registration: Rp 4.000.000</li><li>Travel expenses: Rp 5.000.000</li><li>Accommodation: Rp 2.000.000</li><li>Publication fee: Rp 1.000.000</li></ul>',
                    'submission_deadline' => Carbon::now()->addDays(60),
                    'expected_publication_date' => Carbon::now()->addMonths(8),
                    'status' => 'draft',
                ]);
            }
        }

        // Buat contoh publikasi yang sudah terbit
        if ($proposals->count() > 0) {
            PublicationFunding::create([
                'proposal_id' => $proposals->first()->id,
                'user_id' => $proposals->first()->user_id,
                'title' => 'Digital Transformation in Islamic Education: Lessons from Indonesia',
                'publication_type' => 'journal_international',
                'journal_name' => 'Journal of Islamic Studies and Technology',
                'publisher' => null,
                'abstract' => '<p>This study examines the digital transformation process in Islamic educational institutions across Indonesia, highlighting successful implementation strategies and challenges encountered during the transition to digital learning environments.</p>',
                'estimated_cost' => 6000000,
                'cost_breakdown' => '<ul><li>Publication fee: Rp 4.000.000</li><li>Translation: Rp 1.500.000</li><li>Editing: Rp 500.000</li></ul>',
                'submission_deadline' => Carbon::now()->subMonths(3),
                'expected_publication_date' => Carbon::now()->subMonths(1),
                'status' => 'published',
                'reviewer_notes' => '<p>Artikel telah berhasil dipublikasikan dan mendapat respon positif dari komunitas akademik internasional.</p>',
                'approved_amount' => 6000000,
                'reviewed_by' => $admin ? $admin->id : null,
                'reviewed_at' => Carbon::now()->subMonths(6),
                'submitted_at' => Carbon::now()->subMonths(8),
                'publication_url' => 'https://journal.islamictech.org/article/2025/digital-transformation',
                'doi' => '10.1234/jist.2025.001',
            ]);
        }

        $this->command->info('PublicationFunding data seeded successfully!');
    }
}
