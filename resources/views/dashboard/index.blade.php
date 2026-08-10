@extends(config('notification-system.views.layout', 'dashboard.master'), ['title' => __('dashboard.notifications')])

@section('content')
<div class="container-fluid p-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.notifications') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('dashboard.manage_notifications_description') ?? 'Manage and send system notifications' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.notifications.compose') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                + {{ __('dashboard.send_notification') }}
            </a>
        </div>
    </div>

    <x-notification-system::statistics-widget :total="$notifications->total()" :unread="$unreadCount" />

    <x-notification-system::table :notifications="$notifications" />

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
