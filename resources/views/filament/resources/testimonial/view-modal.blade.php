<div class="space-y-4">
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Name</div>
        <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $record->name }}</div>
    </div>

    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Quote</div>
        <div class="text-base text-gray-900 dark:text-white prose dark:prose-invert max-w-none">
            {!! \Illuminate\Support\Str::markdown($record->quote) !!}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</div>
            <div class="flex items-center gap-2">
                @if($record->is_active)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                        Active
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                        Inactive
                    </span>
                @endif
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Sort Order</div>
            <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $record->sort_order }}</div>
        </div>
    </div>

    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Created At</div>
        <div class="text-base text-gray-900 dark:text-white">{{ $record->created_at->format('M d, Y h:i A') }}</div>
    </div>
</div>