<?php

namespace NotificationSystem\Facades;

use Illuminate\Support\Facades\Facade;
use NotificationSystem\Builders\NotificationBuilder;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\Services\NotificationManager;

/**
 * Facade for the NotificationSystem package.
 *
 * @method static NotificationBuilder make()
 * @method static NotificationManager extend(string $name, ChannelInterface|\Closure|string $channel)
 * @method static array send(\NotificationSystem\DTOs\NotificationData $notification, mixed $target, bool $queue = true, ?int $delaySeconds = null)
 * @method static \NotificationSystem\Channels\ChannelManager getChannelManager()
 *
 * @see \NotificationSystem\Services\NotificationManager
 */
class NotificationSystem extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return NotificationManager::class;
    }
}
