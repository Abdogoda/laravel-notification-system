<?php

namespace NotificationSystem\Tests\Feature;

use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Models\NotificationLog;
use NotificationSystem\Services\DeliveryLogger;
use NotificationSystem\Tests\TestCase;

class LoggingTest extends TestCase
{
    public function test_delivery_logger_creates_notification_log_record()
    {
        $logger = new DeliveryLogger();
        $notification = new NotificationData(id: 'notif-99', title: 'Test Title', body: 'Test Body');
        $recipient = new RecipientData(id: 42, type: 'App\Models\User', email: 'user@test.com');

        $logger->logAttempt(
            notification: $notification,
            recipient: $recipient,
            channel: 'mail',
            status: 'delivered',
            response: ['status' => 'success'],
            durationMs: 150
        );

        $this->assertDatabaseHas('notification_logs', [
            'notification_id' => 'notif-99',
            'recipient_id'    => '42',
            'channel'         => 'mail',
            'status'          => 'delivered',
            'duration_ms'     => 150,
        ]);
    }
}
