<x-filament-panels::page>
    <div class="space-y-6">
        @foreach ($this->getRoles() as $role)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Role Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <x-filament::badge :color="$role['color']" size="lg">
                            {{ $role['name'] }}
                        </x-filament::badge>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $role['description'] }}
                    </p>
                </div>

                <!-- Permissions Matrix -->
                <div class="p-6">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Permissions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($role['permissions'] as $permission => $granted)
                            <div class="flex items-center gap-2 p-3 rounded-lg {{ $granted ? 'bg-success-50 dark:bg-success-500/10' : 'bg-gray-50 dark:bg-gray-900' }}">
                                @if ($granted)
                                    <x-heroicon-o-check-circle class="w-5 h-5 text-success-600 dark:text-success-400" />
                                @else
                                    <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400 dark:text-gray-600" />
                                @endif
                                <span class="text-sm {{ $granted ? 'text-success-700 dark:text-success-300 font-medium' : 'text-gray-500 dark:text-gray-500' }}">
                                    {{ $permission }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Info Box -->
        <div class="bg-primary-50 dark:bg-primary-500/10 border border-primary-200 dark:border-primary-500/20 rounded-xl p-6">
            <div class="flex gap-3">
                <x-heroicon-o-information-circle class="w-6 h-6 text-primary-600 dark:text-primary-400 flex-shrink-0" />
                <div>
                    <h4 class="text-sm font-semibold text-primary-900 dark:text-primary-100 mb-1">
                        Need custom roles?
                    </h4>
                    <p class="text-sm text-primary-700 dark:text-primary-300">
                        These are the standard roles available for your team. If you need custom roles with specific permissions, please contact your platform administrator.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
