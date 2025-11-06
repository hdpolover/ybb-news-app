<div class="relative" x-data="{ open: @entangle('isOpen') }">
    @if(count($availableTenants) > 1)
        <!-- Tenant Switcher Button -->
        <button 
            @click="open = !open"
            type="button"
            class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
        >
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <div class="text-left">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Current Workspace</div>
                    <div class="font-semibold">{{ $currentTenant ? $currentTenant->name : 'Select Tenant' }}</div>
                </div>
            </div>
            <svg class="w-5 h-5 ml-2 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div 
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700"
            style="display: none;"
        >
            <div class="py-1">
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Switch Workspace
                </div>
                @foreach($availableTenants as $tenant)
                    <button
                        wire:click="switchTenant('{{ $tenant['id'] }}')"
                        type="button"
                        class="flex items-center w-full px-4 py-2 text-sm text-left hover:bg-gray-100 dark:hover:bg-gray-700 {{ $currentTenant && $currentTenant->id === $tenant['id'] ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200' }}"
                    >
                        <div class="flex items-center flex-1">
                            <div class="flex-shrink-0 w-8 h-8 mr-3">
                                @if(isset($tenant['logo_url']) && $tenant['logo_url'])
                                    <img src="{{ Storage::disk('public')->url($tenant['logo_url']) }}" alt="{{ $tenant['name'] }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    <div class="flex items-center justify-center w-full h-full text-white rounded-full bg-primary-600">
                                        {{ substr($tenant['name'], 0, 2) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">{{ $tenant['name'] }}</div>
                                @if(isset($tenant['subdomain']))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant['subdomain'] }}</div>
                                @endif
                            </div>
                            @if($currentTenant && $currentTenant->id === $tenant['id'])
                                <svg class="w-5 h-5 ml-2 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @else
        <!-- Single Tenant Display -->
        <div class="flex items-center px-4 py-2 space-x-3 text-sm bg-white border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <div class="text-left">
                <div class="text-xs text-gray-500 dark:text-gray-400">Workspace</div>
                <div class="font-semibold text-gray-700 dark:text-gray-200">{{ $currentTenant ? $currentTenant->name : 'No Tenant' }}</div>
            </div>
        </div>
    @endif
</div>
