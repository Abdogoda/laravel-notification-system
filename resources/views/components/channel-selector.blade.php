@props(['selectedChannels' => ['database', 'mail', 'fcm']])

<div class="space-y-4">
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
        {{ __('dashboard.send_via') }}
    </label>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="send_via[]" value="fcm" checked class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">FCM Push</span>
        </label>
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="send_via[]" value="email" class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">Email</span>
        </label>
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="send_via[]" value="whatsapp" class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">WhatsApp</span>
        </label>
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="send_via[]" value="database" checked class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">Database</span>
        </label>
    </div>
</div>
