<?php

namespace NotificationSystem\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * REST API controller for notification management.
 *
 * Provides endpoints for listing, reading, marking, and deleting
 * notifications for the currently authenticated user across any guard.
 */
class NotificationApiController extends Controller
{
    /**
     * List paginated notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $notifiable = $this->resolveNotifiable($request);

        if (! $notifiable) {
            return $this->response(401, __('notification-system::notifications.unauthenticated'), []);
        }

        $perPage = $request->integer('per_page', $request->integer('paginate', 20));
        $notifications = $notifiable->notifications()->latest()->paginate($perPage);

        $resourceClass = config('notification-system.routes.api.resource_class');

        $items = ($resourceClass && class_exists($resourceClass))
            ? $resourceClass::collection($notifications)
            : $notifications->items();

        return $this->response(
            200,
            __('notification-system::notifications.notifications_fetched'),
            [
                'notifications' => $items,
                'unread_count'  => $notifiable->unreadNotifications()->count(),
            ],
            [
                'total'        => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'count'        => $notifications->count(),
            ]
        );
    }

    /**
     * Get the unread notification count for the authenticated user.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $notifiable = $this->resolveNotifiable($request);

        if (! $notifiable) {
            return $this->response(401, __('notification-system::notifications.unauthenticated'), []);
        }

        return $this->response(200, __('notification-system::notifications.notifications_fetched'), [
            'unread_count' => $notifiable->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notifiable = $this->resolveNotifiable($request);

        if (! $notifiable) {
            return $this->response(401, __('notification-system::notifications.unauthenticated'), []);
        }

        $notification = $notifiable->notifications()->where('id', $id)->first();

        if (! $notification) {
            return $this->response(404, __('notification-system::notifications.notification_not_found'), []);
        }

        $notification->markAsRead();

        return $this->response(200, __('notification-system::notifications.notification_marked_read'), [
            'notification' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $notifiable = $this->resolveNotifiable($request);

        if (! $notifiable) {
            return $this->response(401, __('notification-system::notifications.unauthenticated'), []);
        }

        $notifiable->unreadNotifications()->update(['read_at' => now()]);

        return $this->response(200, __('notification-system::notifications.all_notifications_marked_read'), []);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notifiable = $this->resolveNotifiable($request);

        if (! $notifiable) {
            return $this->response(401, __('notification-system::notifications.unauthenticated'), []);
        }

        $notification = $notifiable->notifications()->where('id', $id)->first();

        if (! $notification) {
            return $this->response(404, __('notification-system::notifications.notification_not_found'), []);
        }

        $notification->delete();

        return $this->response(200, __('notification-system::notifications.notification_deleted'), []);
    }

    /**
     * Clear all notifications for the authenticated user.
     */
    public function clear(Request $request): JsonResponse
    {
        $notifiable = $this->resolveNotifiable($request);

        if (! $notifiable) {
            return $this->response(401, __('notification-system::notifications.unauthenticated'), []);
        }

        $notifiable->notifications()->delete();

        return $this->response(200, __('notification-system::notifications.all_notifications_cleared'), []);
    }

    /**
     * Resolve the notifiable model from the request across all configured guards.
     */
    protected function resolveNotifiable(Request $request): ?Model
    {
        if ($request->user()) {
            return $request->user();
        }

        $guards = array_keys(config('notification-system.guards', []));
        foreach ($guards as $guard) {
            if ($user = $request->user($guard)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Build a standardized JSON response.
     *
     * @param  int     $status      HTTP status code.
     * @param  string  $message     Human-readable message.
     * @param  mixed   $data        Response payload.
     * @param  array   $pagination  Pagination metadata.
     */
    protected function response(int $status, string $message, mixed $data = [], array $pagination = []): JsonResponse
    {
        $payload = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ];

        if (! empty($pagination)) {
            $payload['pagination'] = $pagination;
        }

        return response()->json($payload, $status);
    }
}
