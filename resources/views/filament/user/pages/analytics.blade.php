<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Info Banner -->
        <div class="bg-primary-50 dark:bg-primary-500/10 border border-primary-200 dark:border-primary-500/20 rounded-xl p-6">
            <div class="flex gap-3">
                <x-heroicon-o-information-circle class="w-6 h-6 text-primary-600 dark:text-primary-400 flex-shrink-0" />
                <div>
                    <h4 class="text-sm font-semibold text-primary-900 dark:text-primary-100 mb-1">
                        About Analytics Data
                    </h4>
                    <p class="text-sm text-primary-700 dark:text-primary-300">
                        Analytics data is collected from your published content. Views and engagement metrics are updated in real-time. 
                        Historical data is available for the last 30 days.
                    </p>
                </div>
            </div>
        </div>

        <!-- Widgets -->
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="[
                'sm' => 1,
                'md' => 2,
                'lg' => 4,
            ]"
        />
    </div>
</x-filament-panels::page>
