<?php

namespace NotificationSystem\Services;

use NotificationSystem\Contracts\DeliveryLoggerInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Models\NotificationLog;
use Throwable;

/**
 * Default implementation of {@see DeliveryLoggerInterface}.
 *
 * Records each delivery attempt to the `notification_logs` database table.
 * Silently suppresses any logging errors so the primary notification
 * delivery flow is never interrupted.
 */
class DeliveryLogger implements DeliveryLoggerInterface
{
    /**
     * Record a delivery attempt in the notification logs table.
     *
     * @param  NotificationData  $notification  The notification being sent.
     * @param  RecipientData     $recipient     The target recipient.
     * @param  string            $channel       The channel name (e.g., 'mail', 'fcm').
     * @param  string            $status        Delivery status: 'sending', 'delivered', or 'failed'.
     * @param  array|null        $response      Channel response payload.
     * @param  Throwable|null    $exception     Exception if delivery failed.
     * @param  int|null          $durationMs    Delivery duration in milliseconds.
     */
    public function logAttempt(
        NotificationData $notification,
        RecipientData $recipient,
        string $channel,
        string $status = 'sending',
        ?array $response = null,
        ?Throwable $exception = null,
        ?int $durationMs = null
    ): void {
        if (! config('notification-system.logging_enabled', true)) {
            return;
        }

        try {
            NotificationLog::create([
                'notification_id' => $notification->id,
                'recipient_type'  => $recipient->type,
                'recipient_id'    => (string) $recipient->id,
                'channel'         => $channel,
                'status'          => $status,
                'attempts'        => 1,
                'delivered_at'    => $status === 'delivered' ? now() : null,
                'failed_at'       => $status === 'failed' ? now() : null,
                'response'        => $response,
                'exception'       => $exception?->getMessage(),
                'duration_ms'     => $durationMs,
                'data'            => [
                    'title' => $notification->title,
                    'body'  => $notification->body,
                    'payload' => $notification->data,
                ],
            ]);
        } catch (Throwable $e) {
            // Silently suppress log DB errors so primary notification flow never breaks
        }
    }
}
