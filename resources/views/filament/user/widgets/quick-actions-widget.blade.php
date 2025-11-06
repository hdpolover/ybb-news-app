<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-2 gap-3">
            <!-- Create New Post -->
            <a href="{{ route('filament.app.resources.posts.create') }}" 
               class="flex flex-col items-center justify-center p-4 space-y-1.5 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-document-plus"
                    class="w-8 h-8 text-primary-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Create Post
                </span>
            </a>

            <!-- Create New Program -->
            <a href="{{ route('filament.app.resources.programs.create') }}" 
               class="flex flex-col items-center justify-center p-4 space-y-1.5 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-academic-cap"
                    class="w-8 h-8 text-warning-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Create Program
                </span>
            </a>

            <!-- Create New Job -->
            <a href="{{ route('filament.app.resources.jobs.create') }}" 
               class="flex flex-col items-center justify-center p-4 space-y-1.5 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-briefcase"
                    class="w-8 h-8 text-info-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Create Job
                </span>
            </a>

            <!-- Upload Media -->
            <a href="{{ route('filament.app.resources.media.create') }}" 
               class="flex flex-col items-center justify-center p-4 space-y-1.5 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-photo"
                    class="w-8 h-8 text-success-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Upload Media
                </span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
