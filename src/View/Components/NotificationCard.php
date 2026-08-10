<?php

namespace NotificationSystem\View\Components;

use Illuminate\View\Component;

/**
 * Blade component that renders a single notification card.
 *
 * Usage: <x-notification-system-notification-card :notification="$notification" :is-unread="true" />
 */
class NotificationCard extends Component
{
    /**
     * @param  mixed  $notification  The notification model instance.
     * @param  bool   $isUnread      Whether the notification is unread.
     */
    public function __construct(
        public mixed $notification,
        public bool $isUnread = false
    ) {}

    public function render()
    {
        return view('notification-system::components.card');
    }
}
