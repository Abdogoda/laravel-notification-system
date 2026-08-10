<?php

namespace NotificationSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

/**
 * Dispatched after all channels have been processed for a recipient.
 */
class NotificationSent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  NotificationData  $notification    The notification that was sent.
     * @param  RecipientData     $recipient       The target recipient.
     * @param  array             $channelResults  Results keyed by channel name.
     */
    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient,
        public array $channelResults = []
    ) {}
}
