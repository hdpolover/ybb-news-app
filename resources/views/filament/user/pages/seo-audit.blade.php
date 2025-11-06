<x-filament-panels::page>
    <div class="space-y-6">
        {{-- SEO Score Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Overall SEO Score</h3>
                </div>
                
                @php
                    $score = $this->getSeoScore();
                    $color = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-yellow-600' : 'text-red-600');
                @endphp
                
                <div class="flex items-center justify-center">
                    <div class="relative w-48 h-48">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle
                                cx="96"
                                cy="96"
                                r="88"
                                stroke="currentColor"
                                stroke-width="12"
                                fill="none"
                                class="text-gray-200 dark:text-gray-700"
                            />
                            <circle
                                cx="96"
                                cy="96"
                                r="88"
                                stroke="currentColor"
                                stroke-width="12"
                                fill="none"
                                stroke-dasharray="{{ 2 * pi() * 88 }}"
                                stroke-dashoffset="{{ 2 * pi() * 88 * (1 - $score / 100) }}"
                                class="{{ $color }}"
                                style="transition: stroke-dashoffset 0.5s ease"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-5xl font-bold {{ $color }}">{{ $score }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($score >= 80)
                            Excellent! Your content is well-optimized.
                        @elseif($score >= 60)
                            Good, but there's room for improvement.
                        @else
                            Needs attention. Review the issues below.
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('filament.app.resources.posts.index') }}" 
                       class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <x-heroicon-o-document-text class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"/>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Review All Posts</span>
                    </a>
                    
                    <button onclick="generateSitemap()" 
                            class="w-full flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <x-heroicon-o-map class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"/>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Generate Sitemap</span>
                    </button>
                    
                    <a href="{{ route('filament.app.pages.analytics') }}" 
                       class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <x-heroicon-o-chart-bar class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"/>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">View Analytics</span>
                    </a>
                </div>
            </div>
        </div>
        
        {{-- SEO Issues --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO Issues Detected</h3>
            
            @php
                $issues = $this->getSeoIssues();
            @endphp
            
            @if(count($issues) === 0)
                <div class="flex items-center justify-center p-8">
                    <div class="text-center">
                        <x-heroicon-o-check-circle class="w-16 h-16 mx-auto text-green-500 mb-3"/>
                        <p class="text-gray-600 dark:text-gray-400">No SEO issues detected! Great job!</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($issues as $issue)
                        <div class="flex items-start p-4 bg-{{ $issue['type'] === 'danger' ? 'red' : ($issue['type'] === 'warning' ? 'yellow' : 'blue') }}-50 dark:bg-{{ $issue['type'] === 'danger' ? 'red' : ($issue['type'] === 'warning' ? 'yellow' : 'blue') }}-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($issue['type'] === 'danger')
                                    <x-heroicon-o-exclamation-circle class="w-6 h-6 text-red-600 dark:text-red-400"/>
                                @elseif($issue['type'] === 'warning')
                                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-yellow-600 dark:text-yellow-400"/>
                                @else
                                    <x-heroicon-o-information-circle class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                                @endif
                            </div>
                            
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $issue['title'] }}</h4>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $issue['description'] }}</p>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400"><strong>Action:</strong> {{ $issue['action'] }}</p>
                            </div>
                            
                            <div class="ml-3 flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $issue['severity'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 
                                       ($issue['severity'] === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 
                                       'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100') }}">
                                    {{ ucfirst($issue['severity']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        {{-- Recent Posts SEO Analysis --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Posts SEO Analysis</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Post Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SEO Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Issues</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getRecentPosts() as $post)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($post['title'], 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold {{ $post['seo_score'] >= 80 ? 'text-green-600' : ($post['seo_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $post['seo_score'] }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">/100</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if(count($post['issues']) > 0)
                                        <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                            @foreach($post['issues'] as $issue)
                                                <div>• {{ $issue }}</div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-green-600">No issues</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ $post['url'] }}" class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300">
                                        Edit Post
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function generateSitemap() {
            if (confirm('This will generate a new sitemap.xml file. Continue?')) {
                // Blade template outputs the route URL
                fetch('{{ route('filament.app.pages.seo-audit') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ action: 'generate-sitemap' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sitemap generated successfully!');
                    } else {
                        alert('Error generating sitemap: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
    </script>
    @endpush
</x-filament-panels::page>
