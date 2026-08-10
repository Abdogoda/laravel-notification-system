<?php

namespace NotificationSystem\DTOs;

/**
 * Immutable data transfer object for structured email notification content.
 *
 * Can be used to provide rich email payloads with action buttons,
 * greeting lines, and attachments.
 */
readonly class MailData
{
    /**
     * @param  string       $subject      Email subject line.
     * @param  string|null  $greeting     Custom greeting (e.g., "Hello John!").
     * @param  string|null  $body         Main body text.
     * @param  array        $lines        Additional body lines.
     * @param  string|null  $logoPath     Path to a custom logo.
     * @param  string|null  $actionText   Call-to-action button text.
     * @param  string|null  $actionUrl    Call-to-action button URL.
     * @param  AttachmentData[]  $attachments  File attachments.
     */
    public function __construct(
        public string $subject,
        public ?string $greeting = null,
        public ?string $body = null,
        public array $lines = [],
        public ?string $logoPath = null,
        public ?string $actionText = null,
        public ?string $actionUrl = null,
        public array $attachments = []
    ) {}

    /**
     * Export the mail data as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'greeting' => $this->greeting,
            'body' => $this->body,
            'lines' => $this->lines,
            'logo_path' => $this->logoPath,
            'action_text' => $this->actionText,
            'action_url' => $this->actionUrl,
            'attachments' => array_map(fn (AttachmentData $att) => $att->toArray(), $this->attachments),
        ];
    }
}
