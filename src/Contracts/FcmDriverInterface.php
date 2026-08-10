<?php

namespace NotificationSystem\Contracts;

/**
 * Contract for FCM (Firebase Cloud Messaging) push notification drivers.
 *
 * Implement this interface to provide your own FCM delivery mechanism.
 * Bind your implementation in a service provider and reference it in
 * the config at `notification-system.channels.fcm.driver_class`.
 *
 * @see \NotificationSystem\Channels\FcmChannel
 */
interface FcmDriverInterface
{
    /**
     * Send a push notification via FCM.
     *
     * @param  string  $token  The recipient's FCM device token.
     * @param  string  $title  The notification title.
     * @param  string  $body   The notification body.
     * @param  array<string, string>  $data  Key-value pairs of additional data (all values must be strings).
     * @return void
     */
    public function sendNotification(string $token, string $title, string $body, array $data = []): void;
}
