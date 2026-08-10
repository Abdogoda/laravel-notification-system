# Changelog

All notable changes to `laravel-notification-system` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-08-05

### Added
- Initial extraction and release of NotificationSystem package.
- Multi-channel support: Database, Mail, FCM Push Notifications, WhatsApp (BULQ API), and extensible Custom Channels.
- Fluent Notification Builder (`NotificationSystem::make()`).
- Recipient resolution supporting single models, collections, arrays, query builders, lazy collections, and authentication guards.
- Immutable DTOs (`NotificationData`, `RecipientData`, `ChannelData`, `AttachmentData`, `MailData`).
- Delivery logs table (`notification_logs`) tracking attempts, duration, exception, response, and status.
- Event lifecycle (`NotificationCreating`, `NotificationSending`, `NotificationSent`, `NotificationFailed`, `ChannelSending`, `ChannelSent`, `ChannelFailed`).
- Admin UI with Blade, Tailwind CSS, Alpine.js, and modular Blade components (`notification-card`, `notification-table`, `notification-form`, `recipient-selector`, `channel-selector`, `statistics-widget`).
- Localized notifications with recipient locale fallback.
- Queue, retry, and delay support per channel.
- REST API endpoints for user and admin notification management.
