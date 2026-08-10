# Laravel Notification System

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abdogoda/laravel-notification-system.svg?style=flat-shadow)](https://packagist.org/packages/abdogoda/laravel-notification-system)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Developer Website](https://img.shields.io/badge/Developer-Abdulrhman_Goda-06b6d4.svg)](https://abdogoda.github.io/AG/)

A production-ready, highly extensible, multi-channel notification framework for Laravel applications developed by **[Abdulrhman Goda](https://abdogoda.github.io/AG/)**. Designed for single and multi-authentication, unlimited guards, customizable recipients, delivery tracking, localization, queued channels, and built-in Blade/Tailwind admin dashboard components.

---

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12, or 13

---

## Features

- **Multi-Channel Architecture**: Native support for `Database`, `Mail`, `FCM Push Notifications`, `WhatsApp (BULQ API)`, and extensible custom channels.
- **Fluent Notification Builder**: Clean API (`NotificationSystem::make()->title(...)->to(...)->send()`).
- **Dynamic Recipient Resolution**: Pass Eloquent models, collections, query builders, arrays, or guard names.
- **Immutable DTO Layer**: Strong type safety with `NotificationData`, `RecipientData`, `ChannelData`, `AttachmentData`, and `MailData`.
- **Delivery Logging**: Detailed logging table (`notification_logs`) tracking status, attempts, response payload, exception trace, and duration.
- **Event-Driven Lifecycle**: Event hooks (`NotificationCreating`, `NotificationSending`, `NotificationSent`, `NotificationFailed`, `ChannelSending`, `ChannelSent`, `ChannelFailed`).
- **Queued & Async Delivery**: Asynchronous queued delivery with configurable backoff, retry, and per-channel queue names.
- **Recipient Localization**: Automatic locale detection from recipient preferences with fallback.
- **Admin Panel & Blade Components**: Ready-to-use Blade components (`<x-notification-card>`, `<x-notification-table>`, `<x-notification-form>`, `<x-recipient-selector>`, `<x-channel-selector>`, `<x-statistics-widget>`).
- **REST API Endpoints**: Production API controllers for mobile and SPA clients.

---

## Installation

### 1. Require via Composer

```bash
composer require abdogoda/laravel-notification-system
```

### 2. Publish Configuration & Migrations

```bash
# Publish everything
php artisan vendor:publish --provider="NotificationSystem\NotificationSystemServiceProvider"

# Or publish individually
php artisan vendor:publish --tag=notification-system-config
php artisan vendor:publish --tag=notification-system-migrations
php artisan vendor:publish --tag=notification-system-views
php artisan vendor:publish --tag=notification-system-translations
php artisan vendor:publish --tag=notification-system-assets
```

### 3. Run Migrations

```bash
php artisan migrate
```

---

## Quick Start

### Basic Fluent Builder

```php
use NotificationSystem\Facades\NotificationSystem;

NotificationSystem::make()
    ->title('Welcome to the Platform!')
    ->body('Thank you for joining our academy.')
    ->channels(['database', 'mail', 'fcm'])
    ->to($user)
    ->locale('ar')
    ->data([
        'action_url' => '/dashboard',
        'type' => 'welcome_onboarding'
    ])
    ->send();
```

### Sending to Multiple Guards / Audiences

```php
NotificationSystem::make()
    ->title('Scheduled Maintenance Alert')
    ->body('The system will undergo scheduled maintenance tonight at 12:00 AM.')
    ->channels(['database', 'fcm'])
    ->to(['students', 'teachers', 'merchants'])
    ->send();
```

### Synchronous Delivery (Skip Queue)

```php
NotificationSystem::make()
    ->title('Urgent Alert')
    ->body('Action required immediately.')
    ->to($user)
    ->sendNow();
```

### Queued with Delay

```php
NotificationSystem::make()
    ->title('Reminder')
    ->body('Your appointment is in 1 hour.')
    ->to($user)
    ->queue(true, delaySeconds: 3600)
    ->send();
```

### Email with Attachments

```php
NotificationSystem::make()
    ->title('Invoice Ready')
    ->body('Your invoice is attached.')
    ->email(send: true, greeting: 'Dear Customer')
    ->attach('/path/to/invoice.pdf', name: 'Invoice.pdf', mime: 'application/pdf')
    ->to($user)
    ->send();
```

---

## Configuration Reference

After publishing, the config file is at `config/notification-system.php`:

### Default Channels

```php
'default_channels' => ['database'],
```

### Locale

```php
'default_locale' => 'ar',
'locale_column'  => 'lang',  // Model attribute for preferred locale
```

### Queue Settings

```php
'queue' => [
    'enabled'       => true,
    'connection'    => env('NOTIFICATION_QUEUE_CONNECTION', 'default'),
    'queue_name'    => env('NOTIFICATION_QUEUE_NAME', 'notifications'),
    'backoff'       => [5, 15, 60],      // Retry backoff in seconds
    'max_tries'     => 3,
    'channel_queues' => [                // Per-channel queue names
        'mail'     => 'emails',
        'fcm'      => 'push-notifications',
        'whatsapp' => 'whatsapp',
    ],
],
```

### Guards Setup

Map guard names to Eloquent models. These are resolved when you pass guard strings to `->to()`:

```php
'guards' => [
    'admin' => [
        'model' => \App\Models\Admin::class,
        'label' => 'Admins',
    ],
    'student' => [
        'model' => \App\Models\Student::class,
        'label' => 'Students',
    ],
],
```

### Channel Credentials

```php
'channels' => [
    'whatsapp' => [
        'api_url' => env('BULQ_WHATSAPP_API_URL'),
        'token'   => env('BULQ_WHATSAPP_TOKEN'),
        'template_name' => env('BULQ_WHATSAPP_TEMPLATE', 'login_otp'),
    ],
    'fcm' => [
        'driver_class' => env('NOTIFICATION_FCM_DRIVER', null),
    ],
    'mail' => [
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'from_name'    => env('MAIL_FROM_NAME', 'Laravel'),
    ],
],
```

### Delivery Logging

```php
'table_name'         => 'notification_logs',
'logging_enabled'    => true,
'log_retention_days' => 90,  // Set to null to keep forever
```

---

## Custom Channels

You can register custom channels that implement `ChannelInterface`:

```php
use NotificationSystem\Contracts\ChannelInterface;
use NotificationSystem\DTOs\NotificationData;
use NotificationSystem\DTOs\RecipientData;

class SlackChannel implements ChannelInterface
{
    public function send(RecipientData $recipient, NotificationData $notification): array|bool
    {
        // Your Slack sending logic here
        return ['status' => 'sent'];
    }
}
```

Register it in a service provider:

```php
use NotificationSystem\Facades\NotificationSystem;

NotificationSystem::extend('slack', SlackChannel::class);
```

Then use it:

```php
NotificationSystem::make()
    ->title('Hello Slack!')
    ->channels(['database', 'slack'])
    ->to($user)
    ->send();
```

---

## FCM Setup

To use FCM push notifications, implement `FcmDriverInterface` and configure it:

```php
use NotificationSystem\Contracts\FcmDriverInterface;

class MyFcmDriver implements FcmDriverInterface
{
    public function sendNotification(string $token, string $title, string $body, array $data = []): void
    {
        // Your Firebase sending logic
    }
}
```

Register in a service provider:

```php
$this->app->bind(FcmDriverInterface::class, MyFcmDriver::class);
```

Or set via config/env:

```env
NOTIFICATION_FCM_DRIVER=App\Services\MyFcmDriver
```

---

## Events

The package dispatches events throughout the notification lifecycle. Listen to them in your `EventServiceProvider`:

| Event | Fired When |
|-------|------------|
| `NotificationCreating` | Before recipients are resolved |
| `NotificationSending` | Before delivery starts for a recipient |
| `NotificationSent` | After all channels deliver for a recipient |
| `NotificationFailed` | If the entire delivery process throws |
| `ChannelSending` | Before a specific channel sends |
| `ChannelSent` | After a specific channel succeeds |
| `ChannelFailed` | After a specific channel fails |

### Example Listener

```php
use NotificationSystem\Events\NotificationSent;

class LogNotificationDelivery
{
    public function handle(NotificationSent $event): void
    {
        logger()->info('Notification delivered', [
            'notification_id' => $event->notification->id,
            'recipient_id'    => $event->recipient->id,
            'channels'        => array_keys($event->channelResults),
        ]);
    }
}
```

---

## REST API Endpoints

All routes are configurable via `notification-system.routes.api`:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/notifications` | List paginated notifications |
| `GET` | `/api/v1/notifications/unread-count` | Get unread count |
| `POST` | `/api/v1/notifications/mark-all-read` | Mark all as read |
| `POST` | `/api/v1/notifications/{id}/mark-read` | Mark one as read |
| `DELETE` | `/api/v1/notifications/{id}` | Delete a notification |
| `DELETE` | `/api/v1/notifications` | Clear all notifications |

---

## Blade Components

Available components (all prefixed with `notification-system`):

```blade
<x-notification-system-notification-card :notification="$notification" :is-unread="true" />
<x-notification-system-notification-table :notifications="$notifications" />
<x-notification-system-notification-form />
<x-notification-system-recipient-selector :guards="$guardModels" />
<x-notification-system-channel-selector :selected-channels="['database', 'mail']" />
<x-notification-system-statistics-widget :total="100" :unread="5" :delivered="90" :failed="5" />
```

---

## Delivery Logging

Every send attempt is logged to the `notification_logs` table with:

- `notification_id` — Links to the notification
- `channel` — Which channel was used
- `status` — `pending`, `sending`, `delivered`, or `failed`
- `duration_ms` — How long the channel took
- `response` — JSON response from the channel
- `exception` — Error message if failed

### Query Log Scopes

```php
use NotificationSystem\Models\NotificationLog;

NotificationLog::delivered()->count();
NotificationLog::failed()->forChannel('mail')->get();
NotificationLog::forRecipient('App\Models\User', 1)->recent(30)->get();
NotificationLog::olderThan(90)->delete(); // Prune old logs
```

---

## Testing

```bash
composer test
```

Or:

```bash
vendor/bin/phpunit
```

The test suite uses SQLite in-memory via [Orchestra Testbench](https://github.com/orchestral/testbench).

---

## Credits & Author

Developed with ❤️ by **[Abdulrhman Goda](https://abdogoda.github.io/AG/)**.

- **Website**: [https://abdogoda.github.io/AG/](https://abdogoda.github.io/AG/)

---

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover a security vulnerability, please see [SECURITY.md](SECURITY.md).

## License

The MIT License (MIT). Copyright © 2026 **Abdulrhman Goda**. Please see [License File](LICENSE) for more information.
