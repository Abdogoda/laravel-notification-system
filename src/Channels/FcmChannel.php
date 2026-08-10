<?php

namespace NotificationSystem\Channels;

use Illuminate\Support\Facades\Log;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\Contracts\FcmDriverInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use Throwable;

/**
 * FCM (Firebase Cloud Messaging) push notification channel.
 *
 * Delegates to an {@see FcmDriverInterface} implementation configured via
 * `notification-system.channels.fcm.driver_class`. When no driver is bound,
 * the channel logs the notification payload instead of sending it.
 */
class FcmChannel implements ChannelInterface
{
    /**
     * Send a push notification to the given recipient.
     *
     * @param  RecipientData     $recipient     The resolved recipient data.
     * @param  NotificationData  $notification  The notification payload.
     * @return array{fcm_token: string, status: string}|false
     */
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        if (empty($recipient->fcmToken)) {
            return false;
        }

        try {
            $fcmData = array_map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return json_encode($value);
                }
                return (string) $value;
            }, $notification->data);

            $driverClass = config('notification-system.channels.fcm.driver_class');

            if ($driverClass && class_exists($driverClass)) {
                $driver = app($driverClass);

                if ($driver instanceof FcmDriverInterface) {
                    $driver->sendNotification(
                        $recipient->fcmToken,
                        $notification->title,
                        $notification->body,
                        $fcmData
                    );
                } else {
                    Log::warning('[FcmChannel] Configured driver does not implement FcmDriverInterface.', [
                        'driver_class' => $driverClass,
                    ]);

                    return false;
                }
            } elseif (app()->bound(FcmDriverInterface::class)) {
                // Fallback: check if a driver is bound to the interface directly
                app(FcmDriverInterface::class)->sendNotification(
                    $recipient->fcmToken,
                    $notification->title,
                    $notification->body,
                    $fcmData
                );
            } else {
                Log::info('[FcmChannel] No FCM driver configured. Notification logged.', [
                    'token' => $recipient->fcmToken,
                    'title' => $notification->title,
                    'body'  => $notification->body,
                ]);
            }

            return [
                'fcm_token' => $recipient->fcmToken,
                'status'    => 'success',
            ];
        } catch (Throwable $e) {
            Log::error('FcmChannel push failed: '.$e->getMessage(), [
                'recipient_id' => $recipient->id,
                'exception'    => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
