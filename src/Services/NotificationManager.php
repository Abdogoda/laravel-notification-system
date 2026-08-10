<?php

namespace NotificationSystem\Services;

use Closure;
use NotificationSystem\Builders\NotificationBuilder;
use NotificationSystem\Channels\ChannelManager;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\Contracts\DeliveryLoggerInterface;
use NotificationSystem\Contracts\RecipientResolverInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\Events\NotificationCreating;
use NotificationSystem\Jobs\SendNotificationJob;

/**
 * Central service that orchestrates notification building, recipient resolution,
 * and dispatching across channels (queued or synchronous).
 */
class NotificationManager
{
    public function __construct(
        protected ChannelManager $channelManager,
        protected RecipientResolverInterface $recipientResolver,
        protected DeliveryLoggerInterface $logger
    ) {}

    /**
     * Create a new fluent notification builder.
     */
    public function make(): NotificationBuilder
    {
        return new NotificationBuilder($this);
    }

    /**
     * Register a custom channel driver.
     *
     * @param  string  $name     The channel name (e.g., 'sms', 'slack').
     * @param  ChannelInterface|Closure|string  $channel  The channel implementation.
     */
    public function extend(string $name, ChannelInterface|Closure|string $channel): self
    {
        $this->channelManager->extend($name, $channel);

        return $this;
    }

    /**
     * Get the underlying channel manager instance.
     */
    public function getChannelManager(): ChannelManager
    {
        return $this->channelManager;
    }

    /**
     * Send a notification to the resolved recipients.
     *
     * @param  NotificationData  $notification   The notification payload DTO.
     * @param  mixed             $target         Recipients target (model, collection, guard, etc.).
     * @param  bool              $queue          Whether to dispatch via queue.
     * @param  int|null          $delaySeconds   Optional delay before processing.
     * @return array<int, array{recipient_id: mixed, status: string}>
     */
    public function send(
        NotificationData $notification,
        mixed $target,
        bool $queue = true,
        ?int $delaySeconds = null
    ): array {
        NotificationCreating::dispatch($notification, $target);

        $recipients = $this->recipientResolver->resolve($target);
        $results = [];

        foreach ($recipients as $recipient) {
            if ($queue && config('notification-system.queue.enabled', true)) {
                $job = new SendNotificationJob($notification, $recipient);

                if ($delaySeconds && $delaySeconds > 0) {
                    $job->delay(now()->addSeconds($delaySeconds));
                }

                dispatch($job);
                $results[] = [
                    'recipient_id' => $recipient->id,
                    'status'       => 'queued',
                ];
            } else {
                // Sync execution
                $job = new SendNotificationJob($notification, $recipient);
                $job->handle($this, $this->logger);

                $results[] = [
                    'recipient_id' => $recipient->id,
                    'status'       => 'dispatched',
                ];
            }
        }

        return $results;
    }
}
