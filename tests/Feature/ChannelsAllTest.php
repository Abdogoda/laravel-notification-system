<?php

namespace NotificationSystem\Tests\Feature;

use Illuminate\Support\Facades\Http;
use NotificationSystem\Channels\ChannelManager;
use NotificationSystem\Channels\FcmChannel;
use NotificationSystem\Channels\WhatsappChannel;
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\Contracts\FcmDriverInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Tests\TestCase;

class MockCustomChannel implements ChannelInterface
{
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        return ['custom_status' => 'sent'];
    }
}

class MockFcmDriver implements FcmDriverInterface
{
    public bool $sent = false;

    public function sendNotification(string $token, string $title, string $body, array $data = []): void
    {
        $this->sent = true;
    }
}

class ChannelsAllTest extends TestCase
{
    public function test_whatsapp_channel_sends_http_request_successfully()
    {
        config()->set('notification-system.channels.whatsapp.api_url', 'https://app.bulq.chat/api/send');
        config()->set('notification-system.channels.whatsapp.token', 'test-token');

        Http::fake([
            '*' => Http::response(['message_id' => '12345'], 200),
        ]);

        $channel = new WhatsappChannel();
        $recipient = new RecipientData(id: 1, phone: '201140158807');
        $notification = new NotificationData(id: 'notif-1', title: 'OTP Code', body: '123456', data: ['code' => '123456']);

        $result = $channel->send($recipient, $notification);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('201140158807', $result['phone']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://app.bulq.chat/api/send'
                && $request['phone_number'] === '201140158807'
                && $request['field_1'] === '123456';
        });
    }

    public function test_whatsapp_channel_returns_false_on_api_failure()
    {
        config()->set('notification-system.channels.whatsapp.api_url', 'https://app.bulq.chat/api/send');
        config()->set('notification-system.channels.whatsapp.token', 'test-token');

        Http::fake([
            'https://app.bulq.chat/api/send' => Http::response(['error' => 'Server Error'], 500),
        ]);

        $channel = new WhatsappChannel();
        $recipient = new RecipientData(id: 1, phone: '201140158807');
        $notification = new NotificationData(id: 'notif-1', title: 'Title', body: 'Body');

        $result = $channel->send($recipient, $notification);
        $this->assertFalse($result);
    }

    public function test_whatsapp_channel_returns_false_when_config_is_missing()
    {
        config()->set('notification-system.channels.whatsapp.api_url', null);
        config()->set('notification-system.channels.whatsapp.token', null);

        $channel = new WhatsappChannel();
        $recipient = new RecipientData(id: 1, phone: '201140158807');
        $notification = new NotificationData(id: 'notif-1', title: 'Title', body: 'Body');

        $result = $channel->send($recipient, $notification);
        $this->assertFalse($result);
    }

    public function test_whatsapp_channel_returns_false_on_empty_phone()
    {
        $channel = new WhatsappChannel();
        $recipient = new RecipientData(id: 1, phone: null);
        $notification = new NotificationData(id: 'notif-1', title: 'Title', body: 'Body');

        $result = $channel->send($recipient, $notification);
        $this->assertFalse($result);
    }

    public function test_fcm_channel_handles_mocked_token()
    {
        $channel = new FcmChannel();
        $recipient = new RecipientData(id: 1, fcmToken: 'token-xyz');
        $notification = new NotificationData(id: 'notif-1', title: 'Push Title', body: 'Push Body');

        $result = $channel->send($recipient, $notification);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('token-xyz', $result['fcm_token']);
    }

    public function test_fcm_channel_uses_custom_fcm_driver()
    {
        $mockDriver = new MockFcmDriver();
        $this->app->instance(MockFcmDriver::class, $mockDriver);

        config([
            'notification-system.channels.fcm.driver_class' => MockFcmDriver::class,
        ]);

        $channel = new FcmChannel();
        $recipient = new RecipientData(id: 1, fcmToken: 'token-xyz');
        $notification = new NotificationData(id: 'notif-1', title: 'Push Title', body: 'Push Body');

        $result = $channel->send($recipient, $notification);

        $this->assertIsArray($result);
        $this->assertTrue($mockDriver->sent);
    }

    public function test_fcm_channel_returns_false_on_empty_token()
    {
        $channel = new FcmChannel();
        $recipient = new RecipientData(id: 1, fcmToken: null);
        $notification = new NotificationData(id: 'notif-1', title: 'Title', body: 'Body');

        $result = $channel->send($recipient, $notification);
        $this->assertFalse($result);
    }

    public function test_channel_manager_registers_and_resolves_custom_channel()
    {
        $manager = new ChannelManager();
        $manager->extend('mock', MockCustomChannel::class);

        $this->assertContains('mock', $manager->getRegisteredChannels());

        $resolved = $manager->resolve('mock');
        $this->assertInstanceOf(MockCustomChannel::class, $resolved);

        $result = $resolved->send(new RecipientData(), new NotificationData(id: '1', title: 'T', body: 'B'));
        $this->assertEquals(['custom_status' => 'sent'], $result);
    }
}

