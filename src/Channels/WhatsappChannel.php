<?php

namespace NotificationSystem\Channels;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use Throwable;

/**
 * Sends notifications via WhatsApp using the BULQ Chat API.
 *
 * Requires `BULQ_WHATSAPP_API_URL` and `BULQ_WHATSAPP_TOKEN`
 * to be configured in the `.env` file or config.
 *
 * @see https://app.bulq.chat/
 */
class WhatsappChannel implements ChannelInterface
{
    /**
     * Send a WhatsApp template message to the recipient.
     *
     * @param  RecipientData     $recipient     The resolved recipient (must have phone).
     * @param  NotificationData  $notification  The notification payload.
     * @return array{phone: string, response: mixed, status: string}|false
     */
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        $phone = $recipient->phone ?? ($notification->data['phone'] ?? null);

        if (empty($phone)) {
            return false;
        }

        $apiUrl = config('notification-system.channels.whatsapp.api_url');
        $token  = config('notification-system.channels.whatsapp.token');

        if (empty($apiUrl) || empty($token)) {
            Log::warning('[WhatsappChannel] Missing API URL or token. Check notification-system.channels.whatsapp config.');

            return false;
        }

        $template = config('notification-system.channels.whatsapp.template_name', 'login_otp');
        $lang   = config('notification-system.channels.whatsapp.language', 'ar');
        $timeout = config('notification-system.channels.whatsapp.timeout', 15);

        $normalizedPhone = $this->normalizePhone($phone);
        $code = (string) ($notification->data['code'] ?? $notification->body);

        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->post($apiUrl, [
                    'phone_number'      => $normalizedPhone,
                    'template_name'     => $template,
                    'template_language' => $lang,
                    'field_1'           => $code,
                ]);

            if ($response->failed()) {
                Log::error('[WhatsappChannel] Failed sending message via BULQ.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'phone'  => $normalizedPhone,
                ]);

                return false;
            }

            return [
                'phone'    => $normalizedPhone,
                'response' => $response->json(),
                'status'   => 'success',
            ];
        } catch (Throwable $e) {
            Log::error('[WhatsappChannel] BULQ API Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Normalize a phone number by removing non-digit characters and leading '+'.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/[^\d]/', '', $phone);

        return ltrim($phone, '+');
    }
}
