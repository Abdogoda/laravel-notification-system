<?php

namespace NotificationSystem\DTOs;

/**
 * Immutable data transfer object representing a notification payload.
 *
 * Carries all the information needed to deliver a notification
 * across one or more channels to resolved recipients.
 */
readonly class NotificationData
{
    /**
     * @param  string       $id           Unique notification identifier (UUID).
     * @param  string       $title        The notification title/subject.
     * @param  string       $body         The notification body/content.
     * @param  array        $data         Arbitrary key-value data payload.
     * @param  array        $channels     Channel names to deliver through (e.g., ['database', 'mail']).
     * @param  string|null  $locale       Preferred locale for this notification.
     * @param  string|null  $greeting     Custom email greeting line.
     * @param  array        $emailLines   Additional lines for the email body.
     * @param  AttachmentData[]  $attachments  Email attachments.
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $body,
        public array $data = [],
        public array $channels = ['database'],
        public ?string $locale = null,
        public ?string $greeting = null,
        public array $emailLines = [],
        public array $attachments = []
    ) {}

    /**
     * Export the notification data as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'channels' => $this->channels,
            'locale' => $this->locale,
            'greeting' => $this->greeting,
            'email_lines' => $this->emailLines,
            'attachments' => array_map(fn (AttachmentData $att) => $att->toArray(), $this->attachments),
        ];
    }
}
