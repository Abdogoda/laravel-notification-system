<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use Throwable;

/**
 * Dispatched when a specific channel fails to deliver a notification.
 */
class ChannelFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient,
        public string $channel,
        public Throwable $exception
    ) {}
}
