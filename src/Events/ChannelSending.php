<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

/**
 * Dispatched before a specific channel attempts to send a notification.
 */
class ChannelSending
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient,
        public string $channel
    ) {}
}
