<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cleaning Report - {{ $property->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Cleaning Report</h1>
                        <p class="text-gray-600">{{ $property->name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">{{ $session->scheduled_date->format('F j, Y') }}</div>
                        <div class="text-sm text-gray-500">Report generated {{ $report->generated_at->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">{{ $completionRate }}%</div>
                        <div class="text-sm text-gray-600">Completion Rate</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">{{ $completedTasks }}/{{ $totalTasks }}</div>
                        <div class="text-sm text-gray-600">Tasks Completed</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600">{{ $totalPhotos }}</div>
                        <div class="text-sm text-gray-600">Photos Uploaded</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-3xl font-bold text-orange-600">{{ $notes->count() }}</div>
                        <div class="text-sm text-gray-600">Notes Added</div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Housekeeper:</span>
                            <span class="ml-2 font-medium">{{ $housekeeper->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Started:</span>
                            <span class="ml-2 font-medium">{{ $session->started_at?->format('M j, Y g:i A') ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Completed:</span>
                            <span class="ml-2 font-medium">{{ $session->ended_at?->format('M j, Y g:i A') ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Duration:</span>
                            <span class="ml-2 font-medium">
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

            {{-- Notes Section --}}
            @if($notes->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Housekeeper Notes</h2>
                    <div class="space-y-3">
                        @foreach($notes as $item)
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="font-medium text-yellow-800">{{ $item->task?->name ?? 'Task' }}</div>
                                <div class="text-sm text-yellow-700 mt-1">{{ $item->note }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Problem Photos --}}
            @if($problemPhotos->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-red-600 mb-4">Problem Photos</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($problemPhotos as $photo)
                            <a href="{{ $photo->high_res_url }}" target="_blank" class="block">
                                <img src="{{ $photo->url }}" alt="Problem photo" class="w-full h-32 object-cover rounded-lg hover:opacity-90 transition-opacity">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Room Photos --}}
            @foreach($rooms as $room)
                @php $roomPhotos = $photosByRoom->get($room->id, collect()); @endphp
                @if($roomPhotos->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $room->name }}</h2>
                        
                        {{-- Room Tasks --}}
                        @php $items = $roomItems->get($room->id, collect()); @endphp
                        @if($items->count() > 0)
                            <div class="mb-4">
                                <div class="text-sm text-gray-600 mb-2">Tasks completed: {{ $items->where('checked', true)->count() }}/{{ $items->count() }}</div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($items as $item)
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $item->checked ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                                <a href="{{ $photo->high_res_url }}" target="_blank" class="relative block group">
                                    <img src="{{ $photo->url }}" alt="Room photo" class="w-full h-32 object-cover rounded-lg group-hover:opacity-90 transition-opacity">
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 text-white text-xs rounded">
                                        {{ $photo->captured_at?->format('H:i') }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Footer --}}
            <div class="text-center text-sm text-gray-500 mt-8">
                <p>This report was generated automatically by the housekeeping management system.</p>
                <p class="mt-1">Report views: {{ $report->view_count }} | Last viewed: {{ $report->last_viewed_at?->diffForHumans() ?? 'Just now' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
