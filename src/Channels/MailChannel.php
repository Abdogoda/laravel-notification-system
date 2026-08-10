<?php

namespace NotificationSystem\Channels;

use Illuminate\Support\Facades\Mail;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

/**
 * Sends notifications via email using Laravel's Mail facade.
 *
 * Uses the `notification-system::emails.base` Blade template
 * (or falls back to `emails.base` in the host app).
 */
class MailChannel implements ChannelInterface
{
    /**
     * Send an email notification to the recipient.
     *
     * @param  RecipientData     $recipient     The resolved recipient (must have email).
     * @param  NotificationData  $notification  The notification payload.
     * @return array{recipient_email: string, status: string}|false
     */
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        if (empty($recipient->email)) {
            return false;
        }

        $view = view()->exists('notification-system::emails.base')
            ? 'notification-system::emails.base'
            : 'emails.base';

        $greeting = $notification->greeting
            ?: __('notification-system::notifications.hello').' '.($recipient->name ?? '').'!';

        Mail::send($view, [
            'locale' => $recipient->locale ?? config('notification-system.default_locale', 'ar'),
            'title'  => $notification->title,
            'greeting' => $greeting,
            'body'   => $notification->body,
            'emailLines' => $notification->emailLines,
            'notifiable' => $recipient->rawModel,
            'logoPath' => config('notification-system.channels.mail.logo'),
        ], function ($message) use ($recipient, $notification) {
            $message->to($recipient->email, $recipient->name ?? '')
                ->subject($notification->title);

            foreach ($notification->attachments as $attachment) {
                $options = array_filter([
                    'as' => $attachment->name,
                    'mime' => $attachment->mime,
                ]);
                $message->attach($attachment->path, $options);
            }
        });

        return [
            'recipient_email' => $recipient->email,
            'status' => 'success',
        ];
    }
}
