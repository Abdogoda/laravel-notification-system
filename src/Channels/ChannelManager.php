<?php

namespace NotificationSystem\Channels;

use Closure;
use InvalidArgumentException;
use NotificationSystem\Contracts\ChannelInterface;

/**
 * Manages notification channel registration and resolution.
 *
 * Core channels (database, mail, fcm, whatsapp) are registered automatically.
 * Custom channels can be added via {@see extend()}.
 */
class ChannelManager
{
    /**
     * Registered channel drivers.
     *
     * @var array<string, ChannelInterface|Closure|string>
     */
    protected array $channels = [];

    public function __construct()
    {
        $this->registerCoreChannels();
    }

    /**
     * Register the built-in core channels.
     */
    protected function registerCoreChannels(): void
    {
        $this->channels['database'] = DatabaseChannel::class;
        $this->channels['mail']     = MailChannel::class;
        $this->channels['fcm']      = FcmChannel::class;
        $this->channels['whatsapp'] = WhatsappChannel::class;
    }

    /**
     * Register or override a custom channel driver.
     *
     * @param  string  $name     The channel identifier (lowercase).
     * @param  ChannelInterface|Closure|string  $channel  An instance, Closure factory, or class name.
     * @return self
     */
    public function extend(string $name, ChannelInterface|Closure|string $channel): self
    {
        $this->channels[strtolower($name)] = $channel;

        return $this;
    }

    /**
     * Resolve a channel instance by name.
     *
     * @param  string  $name  The channel name to resolve.
     * @return ChannelInterface
     *
     * @throws InvalidArgumentException If the channel is not registered or invalid.
     */
    public function resolve(string $name): ChannelInterface
    {
        $key = strtolower($name);

        if (! isset($this->channels[$key])) {
            throw new InvalidArgumentException("Notification channel [{$name}] is not registered.");
        }

        $channel = $this->channels[$key];

        if ($channel instanceof ChannelInterface) {
            return $channel;
        }

        if ($channel instanceof Closure) {
            return $channel();
        }

        if (is_string($channel) && class_exists($channel)) {
            return app($channel);
        }

        throw new InvalidArgumentException("Invalid channel resolver for [{$name}].");
    }

    /**
     * Check if a channel is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->channels[strtolower($name)]);
    }

    /**
     * Get all registered channel names.
     *
     * @return array<string>
     */
    public function getRegisteredChannels(): array
    {
        return array_keys($this->channels);
    }
}
