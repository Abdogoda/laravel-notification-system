<?php

namespace NotificationSystem\Builders;

use Illuminate\Support\Str;
use NotificationSystem\DTOs\AttachmentData;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\Services\NotificationManager;

/**
 * Fluent builder for constructing and sending notifications.
 *
 * Usage:
 * ```php
 * NotificationSystem::make()
 *     ->title('Welcome!')
 *     ->body('Thanks for joining.')
 *     ->channels(['database', 'mail'])
 *     ->to($user)
 *     ->send();
 * ```
 */
class NotificationBuilder
{
    protected string $id;

    protected string $title = '';

    protected string $body = '';

    protected array $data = [];

    protected array $channels = ['database'];

    protected ?string $locale = null;

    protected ?string $greeting = null;

    protected array $emailLines = [];

    protected array $attachments = [];

    protected mixed $recipientsTarget = null;

    protected bool $shouldQueue = false;

    protected ?int $delaySeconds = null;

    /**
     * Create a new builder instance.
     */
    public function __construct(protected NotificationManager $manager)
    {
        $this->id = (string) Str::uuid();
        $this->locale = config('notification-system.default_locale', 'ar');
    }

    /**
     * Create a new builder instance (static factory).
     */
    public static function make(?NotificationManager $manager = null): static
    {
        return new static($manager ?? app(NotificationManager::class));
    }

    /**
     * Set a custom notification ID.
     */
    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the notification title (supports translation parameters).
     */
    public function title(string $title, array $params = []): static
    {
        $this->title = ! empty($params) ? __($title, $params) : __($title);

        return $this;
    }

    /**
     * Set the notification body (supports translation parameters).
     */
    public function body(string $body, array $params = []): static
    {
        $this->body = ! empty($params) ? __($body, $params) : __($body);

        return $this;
    }

    /**
     * Set the delivery channels.
     *
     * @param  array<string>  $channels  e.g. ['database', 'mail', 'fcm', 'whatsapp']
     */
    public function channels(array $channels): static
    {
        $this->channels = array_values(array_unique($channels));

        return $this;
    }

    /**
     * Set the recipients target.
     *
     * Accepts: Eloquent Model, Collection, array, query builder,
     * guard name string, array of guard names, Closure, or RecipientData.
     */
    public function to(mixed $recipients): static
    {
        $this->recipientsTarget = $recipients;

        return $this;
    }

    /**
     * Set the notification locale.
     */
    public function locale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Merge additional data into the notification payload.
     *
     * @param  array<string, mixed>  $data
     */
    public function data(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Enable email delivery and optionally set email-specific content.
     *
     * @param  bool         $send      Whether to add 'mail' to channels.
     * @param  array        $lines     Additional email body lines.
     * @param  string|null  $greeting  Custom greeting line.
     */
    public function email(bool $send = true, array $lines = [], ?string $greeting = null): static
    {
        if (! empty($lines)) {
            $this->emailLines = $lines;
        }

        if ($greeting) {
            $this->greeting = $greeting;
        }

        if ($send && ! in_array('mail', $this->channels)) {
            $this->channels[] = 'mail';
        }

        return $this;
    }

    /**
     * Set a custom email greeting.
     */
    public function greeting(string $greeting): static
    {
        $this->greeting = $greeting;

        return $this;
    }

    /**
     * Set additional email body lines.
     *
     * @param  array<string>  $lines
     */
    public function emailLines(array $lines): static
    {
        $this->emailLines = $lines;

        return $this;
    }

    /**
     * Attach a file to the email notification.
     */
    public function attach(string $path, ?string $name = null, ?string $mime = null): static
    {
        $this->attachments[] = new AttachmentData($path, $name, $mime);

        return $this;
    }

    /**
     * Enable queued delivery with optional delay.
     *
     * @param  bool      $queue         Whether to dispatch via queue.
     * @param  int|null  $delaySeconds  Delay in seconds before processing.
     */
    public function queue(bool $queue = true, ?int $delaySeconds = null): static
    {
        $this->shouldQueue = $queue;
        $this->delaySeconds = $delaySeconds;

        return $this;
    }

    /**
     * Build the immutable NotificationData DTO from builder state.
     */
    public function buildDTO(): NotificationData
    {
        return new NotificationData(
            id: $this->id,
            title: $this->title,
            body: $this->body,
            data: $this->data,
            channels: $this->channels,
            locale: $this->locale,
            greeting: $this->greeting,
            emailLines: $this->emailLines,
            attachments: $this->attachments
        );
    }

    /**
     * Build the DTO and send the notification (queued by default).
     *
     * @return array<int, array{recipient_id: mixed, status: string}>
     */
    public function send(): mixed
    {
        $dto = $this->buildDTO();

        return $this->manager->send(
            notification: $dto,
            target: $this->recipientsTarget,
            queue: $this->shouldQueue,
            delaySeconds: $this->delaySeconds
        );
    }

    /**
     * Build the DTO and send the notification synchronously (bypass queue).
     *
     * @return array<int, array{recipient_id: mixed, status: string}>
     */
    public function sendNow(): mixed
    {
        $dto = $this->buildDTO();

        return $this->manager->send(
            notification: $dto,
            target: $this->recipientsTarget,
            queue: false
        );
    }
}
