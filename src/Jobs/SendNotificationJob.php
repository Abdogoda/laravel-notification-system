<?php

namespace NotificationSystem\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NotificationSystem\Contracts\DeliveryLoggerInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Events\ChannelFailed;
use NotificationSystem\Events\ChannelSending;
use NotificationSystem\Events\ChannelSent;
use NotificationSystem\Events\NotificationFailed;
use NotificationSystem\Events\NotificationSending;
use NotificationSystem\Events\NotificationSent;
use NotificationSystem\Services\NotificationManager;
use Throwable;

/**
 * Queued job that delivers a notification to a single recipient
 * across all specified channels.
 *
 * Queue connection, queue name, backoff, and max tries are all
 * pulled from the `notification-system.queue` config on construction.
 */
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int Maximum number of attempts before marking as failed. */
    public int $tries;

    /** @var array<int, int> Backoff intervals in seconds between retry attempts. */
    public array $backoff;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public NotificationData $notification,
        public RecipientData $recipient
    ) {
        $queueConfig = config('notification-system.queue', []);

        $this->tries   = $queueConfig['max_tries'] ?? 3;
        $this->backoff = $queueConfig['backoff'] ?? [5, 15, 60];

        $connection = $queueConfig['connection'] ?? null;
        if ($connection && $connection !== 'default') {
            $this->onConnection($connection);
        }

        $queueName = $queueConfig['queue_name'] ?? 'notifications';
        $this->onQueue($queueName);
    }

    /**
     * Execute the job — deliver the notification across all channels.
     */
    public function handle(NotificationManager $manager, DeliveryLoggerInterface $logger): void
    {
        NotificationSending::dispatch($this->notification, $this->recipient);

        $results = [];
        $channelQueues = config('notification-system.queue.channel_queues', []);

        try {
            foreach ($this->notification->channels as $channelName) {
                ChannelSending::dispatch($this->notification, $this->recipient, $channelName);

                try {
                    $channel = $manager->getChannelManager()->resolve($channelName);
                    $chStartTime = microtime(true);

                    $result = $channel->send($this->recipient, $this->notification);
                    $chDuration = (int) ((microtime(true) - $chStartTime) * 1000);

                    $status = $result !== false ? 'delivered' : 'failed';
                    $response = is_array($result) ? $result : ['result' => $result];

                    $logger->logAttempt(
                        notification: $this->notification,
                        recipient: $this->recipient,
                        channel: $channelName,
                        status: $status,
                        response: $response,
                        durationMs: $chDuration
                    );

                    $results[$channelName] = $result;

                    if ($result !== false) {
                        ChannelSent::dispatch($this->notification, $this->recipient, $channelName, $result);
                    } else {
                        ChannelFailed::dispatch($this->notification, $this->recipient, $channelName, new \Exception("Channel [{$channelName}] failed delivery."));
                    }
                } catch (Throwable $e) {
                    $logger->logAttempt(
                        notification: $this->notification,
                        recipient: $this->recipient,
                        channel: $channelName,
                        status: 'failed',
                        exception: $e
                    );

                    ChannelFailed::dispatch($this->notification, $this->recipient, $channelName, $e);
                }
            }

            NotificationSent::dispatch($this->notification, $this->recipient, $results);
        } catch (Throwable $e) {
            NotificationFailed::dispatch($this->notification, $this->recipient, $e);
            throw $e;
        }
    }
}
