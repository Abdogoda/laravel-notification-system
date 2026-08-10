<?php

namespace NotificationSystem\Tests\Feature;

use NotificationSystem\Channels\ChannelManager;
use NotificationSystem\Contracts\DeliveryLoggerInterface;
use NotificationSystem\Contracts\RecipientResolverInterface;
use NotificationSystem\Services\DeliveryLogger;
use NotificationSystem\Services\NotificationManager;
use NotificationSystem\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_registers_singleton_bindings()
    {
        $this->assertTrue(app()->bound(ChannelManager::class));
        $this->assertTrue(app()->bound(RecipientResolverInterface::class));
        $this->assertTrue(app()->bound(DeliveryLoggerInterface::class));
        $this->assertTrue(app()->bound(NotificationManager::class));

        $manager1 = app(NotificationManager::class);
        $manager2 = app(NotificationManager::class);

        $this->assertSame($manager1, $manager2);
    }

    public function test_service_provider_merges_default_configuration()
    {
        $config = config('notification-system');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('default_channels', $config);
        $this->assertArrayHasKey('guards', $config);
        $this->assertArrayHasKey('channels', $config);
        $this->assertEquals('notification_logs', config('notification-system.table_name'));
    }
}
