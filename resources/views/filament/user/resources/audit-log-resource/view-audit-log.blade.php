<div class="p-6">
    <div class="space-y-6">
        {{-- Basic Info --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Information</h3>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->user?->name ?? 'System' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Event</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ in_array($record->event, ['created', 'published', 'approved']) ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                               (in_array($record->event, ['updated', 'restored']) ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 
                               (in_array($record->event, ['deleted', 'rejected', 'unpublished']) ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 
                               'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100')) }}">
                            {{ ucfirst($record->event) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resource Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ class_basename($record->auditable_type) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('M j, Y g:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->ip_address ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User Agent</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white truncate" title="{{ $record->user_agent }}">
                        {{ $record->user_agent ?? 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Description --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $record->description }}</p>
        </div>

        {{-- Changed Fields --}}
        @if(count($record->changed_fields) > 0)
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Changed Fields</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Old Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($record->changed_fields as $field => $values)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ ucwords(str_replace('_', ' ', $field)) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                            {{ is_array($values['old']) ? json_encode($values['old']) : ($values['old'] ?? 'null') }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        <code class="bg-green-100 dark:bg-green-900 px-2 py-1 rounded">
                                            {{ is_array($values['new']) ? json_encode($values['new']) : ($values['new'] ?? 'null') }}
                                        </code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Metadata --}}
        @if($record->metadata)
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Additional Information</h3>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg text-xs text-gray-900 dark:text-gray-100 overflow-x-auto">{{ json_encode($record->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
</div>
