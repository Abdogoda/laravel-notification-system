<?php

namespace NotificationSystem\Tests\Feature;

use Illuminate\Support\Facades\Event;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Events\NotificationSent;
use NotificationSystem\Jobs\SendNotificationJob;

use NotificationSystem\Services\DeliveryLogger;
use NotificationSystem\Services\NotificationManager;
use NotificationSystem\Tests\TestCase;

class QueueJobTest extends TestCase
{
    public function test_send_notification_job_processes_channel_delivery_and_dispatches_events()
    {
        Event::fake([NotificationSent::class]);

        $notification = new NotificationData(id: 'job-10', title: 'Job Title', body: 'Job Body', channels: ['fcm']);
        $recipient = new RecipientData(id: 77, fcmToken: 'token-abc');

        $job = new SendNotificationJob($notification, $recipient);
        $manager = app(NotificationManager::class);
        $logger = app(DeliveryLogger::class);

        $job->handle($manager, $logger);

        Event::assertDispatched(NotificationSent::class, function ($event) {
            return $event->notification->id === 'job-10' && $event->recipient->id === 77;
        });
    }
}
