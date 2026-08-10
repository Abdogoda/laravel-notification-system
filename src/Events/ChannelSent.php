<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

/**
 * Dispatched after a specific channel successfully delivers a notification.
 */
class ChannelSent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  NotificationData  $notification  The notification sent.
     * @param  RecipientData     $recipient     The target recipient.
     * @param  string            $channel       The channel name.
     * @param  array|bool        $result        The channel's return value.
     */
    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient,
        public string $channel,
        public array|bool $result = true
    ) {}
}
