<?php

namespace NotificationSystem\Channels;

use Illuminate\Database\Eloquent\Model;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

/**
 * Stores notifications in the database via Laravel's built-in
 * `DatabaseNotification` morphMany relationship.
 */
class DatabaseChannel implements ChannelInterface
{
    /**
     * Persist the notification to the recipient's `notifications` table.
     *
     * @param  RecipientData     $recipient     The resolved recipient.
     * @param  NotificationData  $notification  The notification payload.
     * @return array{database_notification_id: string, status: string}|false
     */
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        if (! ($recipient->rawModel instanceof Model)) {
            return false;
        }

        if (! method_exists($recipient->rawModel, 'notifications')) {
            return false;
        }

        $notificationType = $notification->data['type']
            ?? config('notification-system.notification_type', 'NotificationSystem\\Notification');

        $notificationModel = $recipient->rawModel->notifications()->create([
            'id' => $notification->id,
            'type' => $notificationType,
            'data' => array_merge([
                'title' => $notification->title,
                'body' => $notification->body,
            ], $notification->data),
            'read_at' => null,
        ]);

        return [
            'database_notification_id' => $notificationModel->id,
            'status' => 'success',
        ];
    }
}
