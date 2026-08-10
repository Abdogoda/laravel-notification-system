<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use Throwable;

/**
 * Dispatched when the entire notification delivery process throws an unrecoverable exception.
 */
class NotificationFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient,
        public Throwable $exception
    ) {}
}
