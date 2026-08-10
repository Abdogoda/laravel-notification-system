<?php

namespace NotificationSystem\DTOs;

/**
 * Immutable data transfer object representing a notification channel's metadata.
 */
readonly class ChannelData
{
    /**
     * @param  string  $name     The channel identifier (e.g., 'mail', 'fcm').
     * @param  string  $driver   The driver name (e.g., 'smtp', 'firebase').
     * @param  array   $options  Driver-specific options.
     */
    public function __construct(
        public string $name,
        public string $driver,
        public array $options = []
    ) {}

    /**
     * Export the channel data as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'driver' => $this->driver,
            'options' => $this->options,
        ];
    }
}
