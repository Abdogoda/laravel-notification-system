<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

/**
 * Dispatched before the delivery job starts processing channels for a recipient.
 */
class NotificationSending
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient
    ) {}
}
