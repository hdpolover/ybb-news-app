<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Create New Post -->
            <a href="{{ route('filament.app.resources.posts.create') }}" 
               class="flex flex-col items-center justify-center p-6 space-y-2 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-document-plus"
                    class="w-12 h-12 text-primary-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Create New Post
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Write a new article or page
                </span>
            </a>

            <!-- Create New Program -->
            <a href="{{ route('filament.app.resources.programs.create') }}" 
               class="flex flex-col items-center justify-center p-6 space-y-2 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-academic-cap"
                    class="w-12 h-12 text-warning-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Create New Program
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Add scholarship or opportunity
                </span>
            </a>

            <!-- Create New Job -->
            <a href="{{ route('filament.app.resources.jobs.create') }}" 
               class="flex flex-col items-center justify-center p-6 space-y-2 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-briefcase"
                    class="w-12 h-12 text-info-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Create New Job
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Post a job opening
                </span>
            </a>

            <!-- Upload Media -->
            <a href="{{ route('filament.app.resources.media.create') }}" 
               class="flex flex-col items-center justify-center p-6 space-y-2 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                <x-filament::icon
                    icon="heroicon-o-photo"
                    class="w-12 h-12 text-success-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Upload Media
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Add images or files
                </span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
