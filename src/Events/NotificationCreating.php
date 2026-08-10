<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;

/**
 * Dispatched before recipients are resolved, allowing listeners
 * to modify or cancel the notification before sending.
 */
class NotificationCreating
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public NotificationData $notification,
        public mixed $recipientsTarget
    ) {}
}
