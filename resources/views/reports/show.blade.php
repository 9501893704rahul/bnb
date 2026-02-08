<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                    Cleaning Report
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $property->name }} - {{ $session->scheduled_date->format('F j, Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($report)
                    <button 
                        type="button" 
                        x-data
                        @click="navigator.clipboard.writeText('{{ $shareUrl }}'); alert('Link copied!')"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Link
                    </button>
                @endif
                <a href="{{ route('reports.download-photos', $session) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download All Photos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Summary Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $completionRate }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $completedTasks }}/{{ $totalTasks }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Tasks Completed</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $totalPhotos }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Photos Uploaded</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $notes->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Notes Added</div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Housekeeper:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $housekeeper->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Started:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $session->started_at?->format('M j, Y g:i A') ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Completed:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $session->ended_at?->format('M j, Y g:i A') ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Duration:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">
                            @if($session->started_at && $session->ended_at)
                                {{ $session->started_at->diffForHumans($session->ended_at, true) }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Share Report --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Share Report</h3>
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Email --}}
                <form action="{{ route('reports.send-email', $session) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Send via Email</label>
                        <input type="email" name="email" placeholder="owner@example.com" required 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <textarea name="message" rows="2" placeholder="Optional message..." 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Send Email
                    </button>
                </form>

                {{-- SMS --}}
                <form action="{{ route('reports.send-sms', $session) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Send via Text Message</label>
                        <input type="tel" name="phone" placeholder="+1 (555) 123-4567" required 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Send Text Message
                    </button>
                </form>
            </div>
        </div>

        {{-- Notes Section --}}
        @if($notes->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Housekeeper Notes</h3>
                <div class="space-y-3">
                    @foreach($notes as $item)
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="font-medium text-yellow-800 dark:text-yellow-200">{{ $item->task?->name ?? 'Task' }}</div>
                            <div class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">{{ $item->note }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Problem Photos --}}
        @if($problemPhotos->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Problem Photos</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($problemPhotos as $photo)
                        <div class="relative group">
                            <img src="{{ $photo->url }}" alt="Problem photo" class="w-full h-32 object-cover rounded-lg">
                            <a href="{{ $photo->high_res_url }}" target="_blank" 
                                class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Room Photos --}}
        @foreach($rooms as $room)
            @php $roomPhotos = $photosByRoom->get($room->id, collect()); @endphp
            @if($roomPhotos->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $room->name }}</h3>
                    
                    {{-- Room Tasks --}}
                    @php $items = $roomItems->get($room->id, collect()); @endphp
                    @if($items->count() > 0)
                        <div class="mb-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Tasks completed: {{ $items->where('checked', true)->count() }}/{{ $items->count() }}</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($items as $item)
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $item->checked ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                        @if($item->checked)
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        @endif
                                        {{ $item->task?->name ?? 'Task' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Photos Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($roomPhotos as $photo)
                            <div class="relative group">
                                <img src="{{ $photo->url }}" alt="Room photo" class="w-full h-32 object-cover rounded-lg">
                                <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 text-white text-xs rounded">
                                    {{ $photo->captured_at?->format('H:i') }}
                                </div>
                                <a href="{{ route('photos.download', [$session, $photo]) }}" 
                                    class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</x-app-layout>
