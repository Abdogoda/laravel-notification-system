<?php

namespace NotificationSystem\Tests\Unit;

use NotificationSystem\DTOs\AttachmentData;
use NotificationSystem\DTOs\ChannelData;
use NotificationSystem\DTOs\MailData;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;
use NotificationSystem\Tests\TestCase;

class DtoTest extends TestCase
{
    public function test_notification_data_dto_is_immutable_and_exports_to_array()
    {
        $attachment = new AttachmentData(path: '/path/file.pdf', name: 'Invoice.pdf', mime: 'application/pdf');

        $dto = new NotificationData(
            id: 'test-123',
            title: 'Hello',
            body: 'World',
            data: ['foo' => 'bar'],
            channels: ['database', 'mail'],
            locale: 'ar',
            greeting: 'Welcome!',
            emailLines: ['Line 1', 'Line 2'],
            attachments: [$attachment]
        );

        $this->assertEquals('test-123', $dto->id);
        $this->assertEquals('Hello', $dto->title);
        $this->assertEquals('World', $dto->body);
        $this->assertEquals(['foo' => 'bar'], $dto->data);
        $this->assertContains('database', $dto->channels);
        $this->assertEquals('ar', $dto->locale);
        $this->assertEquals('Welcome!', $dto->greeting);
        $this->assertCount(2, $dto->emailLines);
        $this->assertCount(1, $dto->attachments);

        $array = $dto->toArray();
        $this->assertIsArray($array);
        $this->assertEquals('test-123', $array['id']);
        $this->assertEquals('Hello', $array['title']);
        $this->assertEquals('/path/file.pdf', $array['attachments'][0]['path']);
    }

    public function test_recipient_data_dto_from_array_and_model()
    {
        $recipient = RecipientData::fromModel([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'fcm_token' => 'fcm_token_sample',
            'lang' => 'en',
        ]);

        $this->assertEquals(1, $recipient->id);
        $this->assertEquals('John Doe', $recipient->name);
        $this->assertEquals('john@example.com', $recipient->email);
        $this->assertEquals('123456789', $recipient->phone);
        $this->assertEquals('fcm_token_sample', $recipient->fcmToken);
        $this->assertEquals('en', $recipient->locale);

        $array = $recipient->toArray();
        $this->assertEquals('john@example.com', $array['email']);
    }

    public function test_channel_data_dto()
    {
        $channel = new ChannelData(name: 'whatsapp', driver: 'bulq', options: ['timeout' => 15]);

        $this->assertEquals('whatsapp', $channel->name);
        $this->assertEquals('bulq', $channel->driver);
        $this->assertEquals(['timeout' => 15], $channel->options);
        $this->assertEquals('whatsapp', $channel->toArray()['name']);
    }

    public function test_mail_data_dto()
    {
        $mail = new MailData(
            subject: 'Subject',
            greeting: 'Hello',
            body: 'Body text',
            lines: ['L1'],
            logoPath: '/logo.png'
        );

        $this->assertEquals('Subject', $mail->subject);
        $this->assertEquals('Hello', $mail->greeting);
        $this->assertEquals('/logo.png', $mail->logoPath);
        $this->assertEquals('Subject', $mail->toArray()['subject']);
    }
}
