<?php

namespace NotificationSystem\Contracts;

use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use Throwable;

interface DeliveryLoggerInterface
{
    /**
     * Record a delivery attempt.
     */
    public function logAttempt(
        NotificationData $notification,
        RecipientData $recipient,
        string $channel,
        string $status = 'sending',
        ?array $response = null,
        ?Throwable $exception = null,
        ?int $durationMs = null
    ): void;
}
