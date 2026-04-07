<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Review;

class ReviewSuspendedNotification extends Notification
{
    use Queueable;

    public $review;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review, string $reason)
    {
        $this->review = $review;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'place_name' => $this->review->place->name ?? 'Lugar',
            'reason' => $this->reason,
            'type_label' => 'review_suspended',
        ];
    }
}
