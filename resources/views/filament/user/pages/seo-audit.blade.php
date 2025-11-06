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
                    $scoreColor = $score >= 80 ? '#10b981' : ($score >= 60 ? '#f59e0b' : '#ef4444');
                    $circumference = 2 * pi() * 88;
                    $offset = $circumference * (1 - $score / 100);
                @endphp
                
                <div class="flex items-center justify-center py-4">
                    <div class="relative" style="width: 200px; height: 200px;">
                        <svg viewBox="0 0 200 200" class="w-full h-full transform -rotate-90">
                            <!-- Background circle -->
                            <circle
                                cx="100"
                                cy="100"
                                r="88"
                                stroke="#e5e7eb"
                                stroke-width="12"
                                fill="none"
                                class="dark:stroke-gray-700"
                            />
                            <!-- Progress circle -->
                            <circle
                                cx="100"
                                cy="100"
                                r="88"
                                stroke="{{ $scoreColor }}"
                                stroke-width="12"
                                fill="none"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}"
                                stroke-linecap="round"
                                style="transition: stroke-dashoffset 0.5s ease"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-5xl font-bold" style="color: {{ $scoreColor }}">{{ $score }}</span>
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
        
        {{-- PageSpeed Insights --}}
        @php
            $pageSpeedData = $this->getPageSpeedData();
        @endphp
        
        @if($pageSpeedData)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics (Google PageSpeed Insights)</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-3xl font-bold {{ $pageSpeedData['performance'] >= 90 ? 'text-green-600' : ($pageSpeedData['performance'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $pageSpeedData['performance'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Performance</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-3xl font-bold {{ $pageSpeedData['accessibility'] >= 90 ? 'text-green-600' : ($pageSpeedData['accessibility'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $pageSpeedData['accessibility'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Accessibility</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-3xl font-bold {{ $pageSpeedData['best_practices'] >= 90 ? 'text-green-600' : ($pageSpeedData['best_practices'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $pageSpeedData['best_practices'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Best Practices</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-3xl font-bold {{ $pageSpeedData['seo'] >= 90 ? 'text-green-600' : ($pageSpeedData['seo'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $pageSpeedData['seo'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">SEO Score</div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Core Web Vitals</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Largest Contentful Paint</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pageSpeedData['core_web_vitals']['lcp'] }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="text-xs text-gray-500 dark:text-gray-400">First Input Delay</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pageSpeedData['core_web_vitals']['fid'] }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Cumulative Layout Shift</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pageSpeedData['core_web_vitals']['cls'] }}</div>
                        </div>
                    </div>
                </div>
                
                @if(count($pageSpeedData['opportunities']) > 0)
                    <div>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Top Optimization Opportunities</h4>
                        <div class="space-y-2">
                            @foreach($pageSpeedData['opportunities'] as $opportunity)
                                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $opportunity['title'] }}</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $opportunity['savings'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    Data cached for 1 hour. <a href="#" onclick="event.preventDefault(); clearPageSpeedCache()" class="text-amber-600 hover:underline">Refresh now</a>
                </div>
            </div>
        @endif
        
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
        
        function clearPageSpeedCache() {
            if (confirm('This will fetch fresh performance data from Google. Continue?')) {
                window.location.href = '{{ route('filament.app.pages.seo-audit') }}?refresh=1';
            }
        }
    </script>
    @endpush
</x-filament-panels::page>
