@props(['guards' => []])

<form method="POST" action="{{ route('dashboard.notifications.send') }}" class="space-y-6">
    @csrf

    <div>
        <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
            {{ __('dashboard.notification_title') }} *
        </label>
        <input type="text" name="title" id="title" required value="{{ old('title') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('dashboard.enter_title') }}">
    </div>

    <div>
        <label for="body" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
            {{ __('dashboard.notification_body') }} *
        </label>
        <textarea name="body" id="body" rows="4" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('dashboard.enter_body') }}">{{ old('body') }}</textarea>
    </div>

    <x-notification-system::recipient-selector :guards="$guards" />

    <x-notification-system::channel-selector />

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
            {{ __('dashboard.send_notification') }}
        </button>
    </div>
</form>
