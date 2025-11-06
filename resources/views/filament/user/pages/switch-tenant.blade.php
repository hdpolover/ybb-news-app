<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->getTenants() as $tenant)
                <div 
                    wire:click="switchTenant('{{ $tenant->id }}')"
                    class="relative cursor-pointer rounded-lg border p-6 transition hover:border-primary-500 hover:shadow-lg {{ $this->getCurrentTenantId() === $tenant->id ? 'border-primary-500 bg-primary-50 dark:bg-primary-950' : 'border-gray-200 dark:border-gray-700' }}"
                >
                    @if ($this->getCurrentTenantId() === $tenant->id)
                        <div class="absolute top-4 right-4">
                            <x-filament::badge color="success">
                                Current
                            </x-filament::badge>
                        </div>
                    @endif

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div 
                                class="flex h-12 w-12 items-center justify-center rounded-lg text-white"
                                {!! 'style="background-color: ' . ($tenant->primary_color ?? '#3B82F6') . '"' !!}
                            >
                                <x-filament::icon
                                    icon="heroicon-o-building-office-2"
                                    class="h-6 w-6"
                                />
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $tenant->name }}
                            </h3>
                            
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $tenant->domain }}
                            </p>

                            @if ($tenant->description)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ Str::limit($tenant->description, 100) }}
                                </p>
                            @endif

                            <div class="mt-3 flex items-center space-x-2">
                                <x-filament::badge 
                                    :color="$tenant->pivot->role === 'tenant_admin' ? 'success' : 'info'"
                                >
                                    {{ ucwords(str_replace('_', ' ', $tenant->pivot->role)) }}
                                </x-filament::badge>

                                @if ($tenant->pivot->is_default)
                                    <x-filament::badge color="warning">
                                        Default
                                    </x-filament::badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($this->getTenants()->isEmpty())
            <div class="text-center py-12">
                <x-filament::icon
                    icon="heroicon-o-building-office-2"
                    class="mx-auto h-12 w-12 text-gray-400"
                />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                    No tenants
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    You don't have access to any tenants yet.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
