@props(['students' => null, 'teachers' => null, 'merchants' => null, 'guards' => []])

<div x-data="{ selectedTargets: ['students'] }" class="space-y-4">
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
        {{ __('dashboard.target_audience') }}
    </label>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="targets[]" value="students" x-model="selectedTargets" class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ __('dashboard.all_students') }}</span>
        </label>
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="targets[]" value="teachers" x-model="selectedTargets" class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ __('dashboard.all_teachers') }}</span>
        </label>
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="targets[]" value="merchants" x-model="selectedTargets" class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ __('dashboard.all_merchants') }}</span>
        </label>
        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <input type="checkbox" name="targets[]" value="admins" x-model="selectedTargets" class="rounded text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ __('dashboard.all_admins') }}</span>
        </label>
    </div>
</div>
