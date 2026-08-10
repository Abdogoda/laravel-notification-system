<?php

namespace NotificationSystem\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for the notification delivery logs table.
 *
 * Tracks every send attempt with status, response payload,
 * exception trace, duration, and metadata.
 *
 * @property int         $id
 * @property string      $notification_id
 * @property string|null $recipient_type
 * @property string|null $recipient_id
 * @property string      $channel
 * @property string      $status
 * @property int         $attempts
 * @property \Carbon\Carbon|null $delivered_at
 * @property \Carbon\Carbon|null $failed_at
 * @property array|null  $response
 * @property string|null $exception
 * @property int|null    $duration_ms
 * @property array|null  $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class NotificationLog extends Model
{
    /** @var array<int, string> */
    protected $guarded = [];

    /** @var array<string, string> */
    protected $casts = [
        'data' => 'array',
        'response' => 'array',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Resolve the table name from config.
     */
    public function getTable(): string
    {
        return config('notification-system.table_name', 'notification_logs');
    }

    // ──────────────────────────────────────────────────────────
    // Query Scopes
    // ──────────────────────────────────────────────────────────

    /**
     * Scope to only delivered logs.
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope to only failed logs.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to only pending/sending logs.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'sending']);
    }

    /**
     * Scope to filter by channel name.
     */
    public function scopeForChannel(Builder $query, string $channel): Builder
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope to filter by recipient (type + id).
     */
    public function scopeForRecipient(Builder $query, string $recipientType, string|int $recipientId): Builder
    {
        return $query->where('recipient_type', $recipientType)
                     ->where('recipient_id', (string) $recipientId);
    }

    /**
     * Scope to filter by notification ID.
     */
    public function scopeForNotification(Builder $query, string $notificationId): Builder
    {
        return $query->where('notification_id', $notificationId);
    }

    /**
     * Scope to filter logs created within the last N days.
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to filter logs older than N days (for pruning).
     */
    public function scopeOlderThan(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }
}
