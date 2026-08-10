@props(['notifications'])

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left rtl:text-right">
        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
            <tr>
                <th scope="col" class="px-4 py-3">{{ __('dashboard.title') }}</th>
                <th scope="col" class="px-4 py-3">{{ __('dashboard.status') }}</th>
                <th scope="col" class="px-4 py-3">{{ __('dashboard.date') }}</th>
                <th scope="col" class="px-4 py-3 text-right rtl:text-left">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
            @forelse($notifications as $notification)
                @php
                    $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                        {{ $data['title'] ?? '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $isUnread ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                            {{ $isUnread ? __('dashboard.unread') : __('dashboard.read') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                        {{ $notification->created_at?->diffForHumans() }}
                    </td>
                    <td class="px-4 py-3 text-right rtl:text-left gap-2">
                        <a href="{{ route('dashboard.notifications.show', $notification->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium text-xs">
                            {{ __('dashboard.view') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        {{ __('dashboard.no_notifications') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
