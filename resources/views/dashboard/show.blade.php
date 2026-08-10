@extends(config('notification-system.views.layout', 'dashboard.master'), ['title' => __('dashboard.notification_details')])

@section('content')
<div class="container-fluid p-4 max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('dashboard.notifications.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
            &larr; {{ __('dashboard.back_to_notifications') ?? 'Back to notifications' }}
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ $data['title'] ?? __('dashboard.notification') }}
            </h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                {{ $notification->created_at?->format('Y-m-d H:i:s') }} ({{ $notification->created_at?->diffForHumans() }})
            </p>
        </div>
        <div class="p-6 text-gray-700 dark:text-gray-200 space-y-4">
            <div>
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('dashboard.body') }}</h4>
                <p class="mt-1 text-base leading-relaxed">{{ $data['body'] ?? ($data['message'] ?? '-') }}</p>
            </div>

            @if(!empty($data['sent_by']))
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500">
                    <span>{{ __('dashboard.sent_by') }}: {{ $data['sent_by'] }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
