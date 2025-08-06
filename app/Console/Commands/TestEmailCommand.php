<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposal;
use App\Models\Review;
use App\Mail\ProposalSubmitted;
use App\Mail\ReviewAssigned;
use App\Mail\ProposalStatusChanged;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {type=all} {--email=admin@lppm.test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications for LPPM system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->option('email');

        $this->info("Testing email notifications...");
        $this->info("Sending to: $email");

        try {
            switch ($type) {
                case 'proposal-submitted':
                    $this->testProposalSubmitted($email);
                    break;
                case 'review-assigned':
                    $this->testReviewAssigned($email);
                    break;
                case 'status-changed':
                    $this->testStatusChanged($email);
                    break;
                default:
                    $this->testAll($email);
                    break;
            }

            $this->info("✅ Email test completed successfully!");

        } catch (\Exception $e) {
            $this->error("❌ Email test failed: " . $e->getMessage());
        }
    }

    private function testProposalSubmitted($email)
    {
        $proposal = Proposal::with(['user', 'researchScheme'])->first();

        if (!$proposal) {
            $this->warn("No proposals found in database");
            return;
        }

        Mail::to($email)->send(new ProposalSubmitted($proposal));
        $this->info("📧 Proposal submitted email sent");
    }

    private function testReviewAssigned($email)
    {
        $review = Review::with(['proposal.user', 'reviewer'])->first();

        if (!$review) {
            $this->warn("No reviews found in database");
            return;
        }

        Mail::to($email)->send(new ReviewAssigned($review));
        $this->info("📧 Review assigned email sent");
    }

    private function testStatusChanged($email)
    {
        $proposal = Proposal::with(['user', 'researchScheme'])->first();

        if (!$proposal) {
            $this->warn("No proposals found in database");
            return;
        }

        Mail::to($email)->send(new ProposalStatusChanged($proposal, 'submitted', 'approved'));
        $this->info("📧 Status changed email sent (approved)");

        Mail::to($email)->send(new ProposalStatusChanged($proposal, 'under_review', 'revision_required'));
        $this->info("📧 Status changed email sent (revision required)");
    }

    private function testAll($email)
    {
        $this->testProposalSubmitted($email);
        $this->testReviewAssigned($email);
        $this->testStatusChanged($email);
    }
}
