@extends(config('notification-system.views.layout', 'dashboard.master'), ['title' => __('dashboard.send_notification')])

@section('content')
<div class="container-fluid p-4 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('dashboard.notifications.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
            &larr; {{ __('dashboard.back_to_notifications') ?? 'Back to notifications' }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ __('dashboard.send_notification') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <x-notification-system::form :guards="$guardModels ?? []" />
    </div>
</div>
@endsection
