<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Mail\ProposalSubmitted;
use App\Mail\ProposalStatusChanged;
use Illuminate\Support\Facades\Mail;

class ProposalObserver
{
    /**
     * Handle the Proposal "created" event.
     */
    public function created(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "updated" event.
     */
    public function updated(Proposal $proposal): void
    {
        // Check if status changed from null/draft to submitted
        if ($proposal->isDirty('submitted_at') && $proposal->submitted_at !== null) {
            // Send proposal submitted email
            Mail::to($proposal->user->email)->send(new ProposalSubmitted($proposal));
        }

        // Check if status changed (excluding first time submitted)
        if ($proposal->isDirty('status') && $proposal->getOriginal('status') !== null) {
            $oldStatus = $proposal->getOriginal('status');
            $newStatus = $proposal->status;

            // Send status change notification
            Mail::to($proposal->user->email)->send(
                new ProposalStatusChanged($proposal, $oldStatus, $newStatus)
            );
        }
    }

    /**
     * Handle the Proposal "deleted" event.
     */
    public function deleted(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "restored" event.
     */
    public function restored(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "force deleted" event.
     */
    public function forceDeleted(Proposal $proposal): void
    {
        //
    }
}
