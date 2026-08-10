@props(['total' => 0, 'unread' => 0, 'delivered' => 0, 'failed' => 0])

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.total_notifications') }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $total }}</p>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.unread') }}</p>
        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $unread }}</p>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.delivered') }}</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $delivered }}</p>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('dashboard.failed') }}</p>
        <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">{{ $failed }}</p>
    </div>
</div>
