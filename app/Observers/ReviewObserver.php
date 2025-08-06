<?php

namespace App\Observers;

use App\Models\Review;
use App\Mail\ReviewAssigned;
use Illuminate\Support\Facades\Mail;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Send email to reviewer when review is assigned
        Mail::to($review->reviewer->email)->send(new ReviewAssigned($review));
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Additional notifications could be added here
        // For example, when review is completed
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
