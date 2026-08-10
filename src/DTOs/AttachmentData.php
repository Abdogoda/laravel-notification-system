<?php

namespace NotificationSystem\DTOs;

/**
 * Immutable data transfer object representing a file attachment for email notifications.
 */
readonly class AttachmentData
{
    /**
     * @param  string       $path     Absolute path to the file.
     * @param  string|null  $name     Display name for the attachment.
     * @param  string|null  $mime     MIME type override.
     * @param  array        $options  Additional attachment options.
     */
    public function __construct(
        public string $path,
        public ?string $name = null,
        public ?string $mime = null,
        public array $options = []
    ) {}

    /**
     * Export the attachment data as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'name' => $this->name,
            'mime' => $this->mime,
            'options' => $this->options,
        ];
    }
}
