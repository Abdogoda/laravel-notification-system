<?php

namespace NotificationSystem\Tests\Feature;

use NotificationSystem\Facades\NotificationSystem;
use NotificationSystem\Tests\TestCase;

class BuilderTest extends TestCase
{
    public function test_fluent_builder_creates_notification_dto_correctly()
    {
        $builder = NotificationSystem::make()
            ->title('Test Notification')
            ->body('Notification Body')
            ->channels(['database', 'mail'])
            ->locale('en')
            ->data(['key' => 'value']);

        $dto = $builder->buildDTO();

        $this->assertEquals('Test Notification', $dto->title);
        $this->assertEquals('Notification Body', $dto->body);
        $this->assertEquals(['database', 'mail'], $dto->channels);
        $this->assertEquals('en', $dto->locale);
        $this->assertEquals(['key' => 'value'], $dto->data);
    }
}
