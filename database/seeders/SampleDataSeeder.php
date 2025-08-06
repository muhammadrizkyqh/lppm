<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ResearchScheme;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\ResearchOutput;
use App\Models\Seminar;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat dosen
        $dosen1 = User::updateOrCreate(
            ['email' => 'ahmad.rifai@stai-ali.ac.id'],
            [
                'name' => 'Dr. Ahmad Rifai, M.Pd',
                'nidn' => '0001018501',
                'password' => bcrypt('password123'),
            ]
        );
        $dosen1->assignRole('dosen');

        $dosen2 = User::updateOrCreate(
            ['email' => 'fatimah.zahra@stai-ali.ac.id'],
            [
                'name' => 'Dr. Fatimah Az-Zahra, M.Ag',
                'nidn' => '0002019002',
                'password' => bcrypt('password123'),
            ]
        );
        $dosen2->assignRole('dosen');

        // Buat reviewer
        $reviewer1 = User::updateOrCreate(
            ['email' => 'mohammad.salim@ui.ac.id'],
            [
                'name' => 'Prof. Dr. Mohammad Salim, M.A',
                'nidn' => '0003015501',
                'password' => bcrypt('password123'),
            ]
        );
        $reviewer1->assignRole('reviewer');

        $reviewer2 = User::updateOrCreate(
            ['email' => 'siti.aminah@uin-sunan-ampel.ac.id'],
            [
                'name' => 'Dr. Siti Aminah, M.Pd',
                'nidn' => '0004016201',
                'password' => bcrypt('password123'),
            ]
        );
        $reviewer2->assignRole('reviewer');

        // Buat proposal
        $scheme1 = ResearchScheme::where('name', 'like', '%Dasar%')->first() ?? ResearchScheme::first();
        $scheme2 = ResearchScheme::where('name', 'like', '%Terapan%')->first() ?? ResearchScheme::skip(1)->first();

        if (!$scheme1 || !$scheme2) {
            $this->command->warn('Research schemes not found. Creating sample schemes...');

            $scheme1 = ResearchScheme::create([
                'name' => 'Penelitian Dasar',
                'description' => 'Penelitian yang bertujuan untuk pengembangan ilmu pengetahuan',
                'max_budget' => 100000000,
                'max_duration' => 24,
                'requirements' => ['Proposal penelitian', 'CV peneliti', 'Surat rekomendasi'],
                'is_active' => true,
            ]);

            $scheme2 = ResearchScheme::create([
                'name' => 'Penelitian Terapan',
                'description' => 'Penelitian yang bertujuan untuk aplikasi praktis',
                'max_budget' => 150000000,
                'max_duration' => 18,
                'requirements' => ['Proposal penelitian', 'CV peneliti', 'MOU dengan mitra'],
                'is_active' => true,
            ]);
        }

        $proposal1 = Proposal::create([
            'title' => 'Pengembangan Model Pembelajaran Berbasis Teknologi Digital dalam Pendidikan Agama Islam di Era Digital',
            'user_id' => $dosen1->id,
            'research_scheme_id' => $scheme1->id,
            'abstract' => 'Penelitian ini bertujuan untuk mengembangkan model pembelajaran berbasis teknologi digital yang efektif untuk mata pelajaran Pendidikan Agama Islam. Dengan perkembangan teknologi yang pesat, pendidikan harus beradaptasi dengan mengintegrasikan teknologi digital dalam proses pembelajaran. Penelitian ini menggunakan metode Research and Development (R&D) untuk menghasilkan model pembelajaran yang inovatif dan efektif.',
            'keywords' => 'pembelajaran digital, pendidikan agama islam, teknologi pendidikan, model pembelajaran',
            'fund_amount' => 75000000,
            'publication_plan' => 'Artikel jurnal nasional terakreditasi dan prosiding seminar internasional',
            'status' => 'under_review',
            'submitted_at' => Carbon::now()->subDays(10),
        ]);

        $proposal2 = Proposal::create([
            'title' => 'Implementasi Hukum Keluarga Islam dalam Masyarakat Modern: Studi Kasus di Wilayah Surabaya',
            'user_id' => $dosen2->id,
            'research_scheme_id' => $scheme2->id,
            'abstract' => 'Penelitian ini mengkaji implementasi hukum keluarga Islam dalam konteks masyarakat modern di wilayah Surabaya. Fokus penelitian adalah bagaimana masyarakat Muslim di Surabaya mengaplikasikan prinsip-prinsip hukum keluarga Islam dalam kehidupan sehari-hari, serta tantangan yang dihadapi dalam penerapannya di era modern.',
            'keywords' => 'hukum keluarga islam, masyarakat modern, fiqh munakahat, implementasi syariah',
            'fund_amount' => 50000000,
            'publication_plan' => 'Buku monograf dan artikel jurnal internasional',
            'status' => 'approved',
            'submitted_at' => Carbon::now()->subDays(30),
            'approved_at' => Carbon::now()->subDays(15),
        ]);

        $proposal3 = Proposal::create([
            'title' => 'Revitalisasi Dakwah Digital melalui Media Sosial untuk Generasi Milenial',
            'user_id' => $dosen1->id,
            'research_scheme_id' => $scheme1->id,
            'abstract' => 'Penelitian ini mengkaji strategi revitalisasi dakwah melalui platform media sosial untuk menjangkau generasi milenial. Dengan perubahan pola komunikasi dan konsumsi informasi generasi milenial, dakwah tradisional perlu beradaptasi dengan memanfaatkan teknologi digital.',
            'keywords' => 'dakwah digital, media sosial, generasi milenial, komunikasi islam',
            'fund_amount' => 60000000,
            'publication_plan' => 'Artikel jurnal nasional dan panduan praktis dakwah digital',
            'status' => 'draft',
            'submitted_at' => null,
        ]);

        // Buat review untuk proposal yang sudah submitted
        $review1 = Review::create([
            'proposal_id' => $proposal1->id,
            'reviewer_id' => $reviewer1->id,
            'assigned_by' => 1, // Admin LPPM
            'relevance_score' => 4,
            'methodology_score' => 4,
            'originality_score' => 4,
            'feasibility_score' => 4,
            'total_score' => 4.0,
            'relevance_comment' => 'Topik penelitian sangat relevan dengan kebutuhan pendidikan saat ini',
            'methodology_comment' => 'Metodologi R&D yang dipilih sudah tepat untuk mencapai tujuan penelitian',
            'originality_comment' => 'Penelitian memiliki aspek kebaruan dalam integrasi teknologi digital dengan PAI',
            'feasibility_comment' => 'Penelitian dapat dilaksanakan dengan sumber daya yang tersedia',
            'general_comment' => 'Proposal penelitian ini memiliki topik yang sangat relevan dengan perkembangan pendidikan saat ini. Metodologi yang digunakan sudah tepat untuk mencapai tujuan penelitian. Namun, perlu diperkuat pada aspek analisis risiko dan mitigasi. Budget yang diajukan cukup realistis untuk scope penelitian ini.',
            'recommendation' => 'approved_with_revision',
            'status' => 'completed',
            'assigned_at' => Carbon::now()->subDays(12),
            'submitted_at' => Carbon::now()->subDays(8),
        ]);

        $review2 = Review::create([
            'proposal_id' => $proposal1->id,
            'reviewer_id' => $reviewer2->id,
            'assigned_by' => 1, // Admin LPPM
            'relevance_score' => 5,
            'methodology_score' => 4,
            'originality_score' => 5,
            'feasibility_score' => 4,
            'total_score' => 4.5,
            'relevance_comment' => 'Penelitian sangat penting untuk pengembangan pendidikan PAI di era digital',
            'methodology_comment' => 'Metodologi penelitian sudah komprehensif dan sistematis',
            'originality_comment' => 'Memiliki kebaruan tinggi dalam pengembangan model pembelajaran digital PAI',
            'feasibility_comment' => 'Tim peneliti memiliki kapasitas yang memadai untuk melaksanakan penelitian',
            'general_comment' => 'Penelitian ini sangat inovatif dan memiliki potensi dampak yang besar bagi pengembangan pendidikan PAI. Metodologi R&D yang dipilih sudah sesuai. Tim peneliti memiliki kapasitas yang memadai. Luaran penelitian akan memberikan kontribusi signifikan bagi dunia pendidikan.',
            'recommendation' => 'approved_without_revision',
            'status' => 'completed',
            'assigned_at' => Carbon::now()->subDays(12),
            'submitted_at' => Carbon::now()->subDays(7),
        ]);

        $review3 = Review::create([
            'proposal_id' => $proposal2->id,
            'reviewer_id' => $reviewer1->id,
            'assigned_by' => 1, // Admin LPPM
            'relevance_score' => 5,
            'methodology_score' => 4,
            'originality_score' => 4,
            'feasibility_score' => 5,
            'total_score' => 4.5,
            'relevance_comment' => 'Topik penelitian sangat penting dan aktual dalam konteks masyarakat modern',
            'methodology_comment' => 'Metodologi kualitatif yang dipilih tepat untuk mengkaji fenomena sosial',
            'originality_comment' => 'Penelitian memberikan perspektif baru dalam implementasi hukum keluarga Islam',
            'feasibility_comment' => 'Penelitian sangat feasible dengan akses yang baik ke subjek penelitian',
            'general_comment' => 'Topik penelitian sangat penting dan aktual. Metodologi kualitatif yang dipilih tepat untuk mengkaji fenomena implementasi hukum keluarga Islam. Peneliti memiliki expertise yang sesuai dengan topik penelitian. Proposal siap untuk dilaksanakan.',
            'recommendation' => 'approved_without_revision',
            'status' => 'completed',
            'assigned_at' => Carbon::now()->subDays(35),
            'submitted_at' => Carbon::now()->subDays(25),
        ]);

        // Buat research output untuk proposal yang approved
        $output1 = ResearchOutput::create([
            'proposal_id' => $proposal2->id,
            'title' => 'Model Adaptif Implementasi Hukum Keluarga Islam di Era Modern',
            'type' => 'jurnal_sinta_3',
            'journal_name' => 'Jurnal Hukum Islam Indonesia',
            'issn' => '2089-5673',
            'description' => 'Artikel jurnal yang membahas temuan penelitian tentang model adaptif implementasi hukum keluarga Islam yang sesuai dengan konteks masyarakat modern di Surabaya.',
            'status' => 'published',
            'publication_date' => Carbon::now()->subDays(5),
            'publication_url' => 'https://journal.uinsa.ac.id/index.php/jhii/article/view/123',
            'verified_by_admin' => true,
            'verified_at' => Carbon::now()->subDays(3),
            'verified_by' => 1, // Admin LPPM
        ]);

        $output2 = ResearchOutput::create([
            'proposal_id' => $proposal2->id,
            'title' => 'Dinamika Hukum Keluarga Islam Kontemporer',
            'type' => 'buku_isbn',
            'isbn' => '978-602-123-456-7',
            'description' => 'Buku monograf hasil penelitian tentang implementasi hukum keluarga Islam dalam masyarakat modern di wilayah Surabaya.',
            'status' => 'submitted',
            'publication_date' => null,
            'verified_by_admin' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        // Hapus bagian seminar untuk menghindari error struktur tabel yang belum pasti
        // $seminar1 = Seminar::create([...]);
        // $seminar2 = Seminar::create([...]);

        $this->command->info('Sample data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Dosen users');
        $this->command->info('- 2 Reviewer users');
        $this->command->info('- 3 Proposals with different statuses');
        $this->command->info('- 3 Reviews with scores and recommendations');
        $this->command->info('- 2 Research outputs');
    }
}
