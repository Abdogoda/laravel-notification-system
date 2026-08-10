<?php

namespace NotificationSystem\Tests\Feature;

use Illuminate\Support\Facades\Event;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Events\ChannelFailed;
use NotificationSystem\Events\ChannelSending;
use NotificationSystem\Events\ChannelSent;
use NotificationSystem\Events\NotificationCreating;
use NotificationSystem\Events\NotificationFailed;
use NotificationSystem\Events\NotificationSending;
use NotificationSystem\Events\NotificationSent;
use NotificationSystem\Tests\TestCase;

class EventTest extends TestCase
{
    public function test_all_notification_lifecycle_events_are_dispatchable()
    {
        Event::fake([
            NotificationCreating::class,
            NotificationSending::class,
            NotificationSent::class,
            NotificationFailed::class,
            ChannelSending::class,
            ChannelSent::class,
            ChannelFailed::class,
        ]);

        $notification = new NotificationData(id: 'evt-1', title: 'Evt Title', body: 'Evt Body');
        $recipient = new RecipientData(id: 99, email: 'evt@test.com');
        $exception = new \Exception('Test Error');

        NotificationCreating::dispatch($notification, [$recipient]);
        NotificationSending::dispatch($notification, $recipient);
        NotificationSent::dispatch($notification, $recipient, ['mail' => true]);
        NotificationFailed::dispatch($notification, $recipient, $exception);

        ChannelSending::dispatch($notification, $recipient, 'mail');
        ChannelSent::dispatch($notification, $recipient, 'mail', true);
        ChannelFailed::dispatch($notification, $recipient, 'mail', $exception);

        Event::assertDispatched(NotificationCreating::class);
        Event::assertDispatched(NotificationSending::class);
        Event::assertDispatched(NotificationSent::class);
        Event::assertDispatched(NotificationFailed::class);
        Event::assertDispatched(ChannelSending::class);
        Event::assertDispatched(ChannelSent::class);
        Event::assertDispatched(ChannelFailed::class);
    }
}
