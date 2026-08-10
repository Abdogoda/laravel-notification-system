<?php

namespace NotificationSystem\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use NotificationSystem\Facades\NotificationSystem;
use NotificationSystem\Http\Requests\SendNotificationRequest;

/**
 * Dashboard controller for managing and sending notifications
 * from the admin panel.
 */
class DashboardNotificationController extends Controller
{
    /**
     * Display a paginated list of the authenticated user's notifications.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $notifications = $user?->notifications()->latest()->paginate(20) ?? collect();
        $unreadCount = $user?->unreadNotifications()->count() ?? 0;

        $view = view()->exists('notification-system::dashboard.index')
            ? 'notification-system::dashboard.index'
            : 'dashboard.notifications.index';

        return view($view, compact('notifications', 'unreadCount'));
    }

    /**
     * Show a single notification's details and mark it as read.
     */
    public function show(Request $request, string $id): View
    {
        $user = auth()->user();
        $notification = $user?->notifications()->where('id', $id)->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $data = is_string($notification->data)
            ? json_decode($notification->data, true)
            : $notification->data;

        $view = view()->exists('notification-system::dashboard.show')
            ? 'notification-system::dashboard.show'
            : 'dashboard.notifications.show';

        return view($view, compact('notification', 'data'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user?->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back()->with('success', __('notification-system::notifications.notification_marked_read'));
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back()->with('success', __('notification-system::notifications.all_notifications_marked_read'));
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user?->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', __('notification-system::notifications.notification_deleted'));
    }

    /**
     * Clear all notifications for the authenticated user.
     */
    public function clear(Request $request): RedirectResponse
    {
        auth()->user()?->notifications()->delete();

        return redirect()->back()->with('success', __('notification-system::notifications.all_notifications_cleared'));
    }

    /**
     * Show the notification compose form with dynamically loaded guard models.
     */
    public function compose(): View
    {
        $guardsConfig = config('notification-system.guards', []);
        $guardModels = [];

        foreach ($guardsConfig as $key => $conf) {
            $modelClass = $conf['model'] ?? null;
            if ($modelClass && class_exists($modelClass)) {
                $guardModels[$key] = [
                    'label' => $conf['label'] ?? ucfirst($key),
                    'items' => $modelClass::orderBy('id', 'desc')->get(),
                ];
            }
        }

        $view = view()->exists('notification-system::dashboard.compose')
            ? 'notification-system::dashboard.compose'
            : 'dashboard.notifications.compose';

        return view($view, compact('guardModels'));
    }

    /**
     * Send a notification to the selected targets via selected channels.
     */
    public function send(SendNotificationRequest $request): RedirectResponse
    {
        $sender = auth()->user();
        $title = $request->title;
        $body = $request->body;
        $targets = $request->targets;
        $sendVia = $request->send_via;

        $meta = [
            'sent_by'    => $sender?->name ?? 'System',
            'sent_by_id' => $sender?->id,
            'sent_at'    => now()->toDateTimeString(),
            'targets'    => $targets,
        ];

        // Resolve recipients dynamically from config guards
        $recipients = collect();
        $guardsConfig = config('notification-system.guards', []);
        $selfGuard = config('notification-system.self_guard');

        foreach ($targets as $targetKey) {
            if (isset($guardsConfig[$targetKey]['model'])) {
                $model = $guardsConfig[$targetKey]['model'];

                // Exclude the sender if this is the self guard
                if ($selfGuard && $targetKey === $selfGuard && $sender) {
                    $recipients = $recipients->merge($model::where('id', '!=', $sender->id)->get());
                } else {
                    $recipients = $recipients->merge($model::all());
                }
            } elseif (str_starts_with($targetKey, 'specific_')) {
                $guardName = str_replace('specific_', '', $targetKey);
                $paramName = $guardName.'_id';
                $id = $request->input($paramName);

                if ($id && isset($guardsConfig[$guardName]['model'])) {
                    $modelClass = $guardsConfig[$guardName]['model'];
                    $item = $modelClass::find($id);
                    if ($item) {
                        $recipients->push($item);
                    }
                }
            }
        }

        NotificationSystem::make()
            ->title($title)
            ->body($body)
            ->channels($sendVia)
            ->data($meta)
            ->to($recipients)
            ->send();

        return back()->with('success', __('notification-system::notifications.notification_sent_success'));
    }

    /**
     * Return the unread notification count as JSON.
     */
    public function unreadCount(): JsonResponse
    {
        $count = auth()->user()?->unreadNotifications()->count() ?? 0;

        return response()->json(['unread_count' => $count]);
    }
}
