<?php

namespace NotificationSystem\Contracts;

use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

interface ChannelInterface
{
    /**
     * Send the notification to the given recipient.
     *
     * @param RecipientData $recipient
     * @param NotificationData $notification
     * @return array|bool
     */
    public function send(RecipientData $recipient, NotificationData $notification): array|bool;
}
