<?php

namespace NotificationSystem\Tests\Feature;

use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Facades\NotificationSystem;
use NotificationSystem\Tests\TestCase;

class DummyDiscordChannel implements ChannelInterface
{
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        return ['status' => 'sent_via_discord'];
    }
}

class ChannelTest extends TestCase
{
    public function test_can_extend_custom_channel()
    {
        NotificationSystem::extend('discord', DummyDiscordChannel::class);

        $channel = app(\NotificationSystem\Channels\ChannelManager::class)->resolve('discord');
        $this->assertInstanceOf(DummyDiscordChannel::class, $channel);

        $result = $channel->send(
            new RecipientData(id: 1, name: 'Test'),
            new NotificationData(id: '1', title: 'T', body: 'B')
        );

        $this->assertEquals(['status' => 'sent_via_discord'], $result);
    }
}
