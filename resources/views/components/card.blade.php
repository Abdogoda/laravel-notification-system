@props(['notification', 'isUnread' => false])

@php
    $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
    $isUnread = $isUnread || is_null($notification->read_at);
@endphp

<div class="p-4 mb-3 rounded-lg border transition-all hover:shadow-md {{ $isUnread ? 'bg-indigo-50/50 border-indigo-200 dark:bg-indigo-950/20 dark:border-indigo-800' : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700' }}">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start space-x-3 rtl:space-x-reverse">
            <div class="p-2 rounded-full {{ $isUnread ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $data['title'] ?? __('dashboard.notification') }}
                </h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    {{ $data['body'] ?? ($data['message'] ?? '') }}
                </p>
                <div class="mt-2 flex items-center text-xs text-gray-400 dark:text-gray-500 gap-3">
                    <span>{{ $notification->created_at?->diffForHumans() }}</span>
                    @if(isset($data['sent_by']))
                        <span>• {{ __('dashboard.sent_by') }}: {{ $data['sent_by'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard.notifications.show', $notification->id) }}" class="px-2.5 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                {{ __('dashboard.view') }}
            </a>
        </div>
    </div>
</div>
