<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                    Upcoming Checkouts
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Properties with guests checking out in the next {{ $days }} days
                </p>
            </div>
            <div class="flex items-center gap-2">
                <select onchange="window.location.href='?days='+this.value" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="3" {{ $days == 3 ? 'selected' : '' }}>Next 3 days</option>
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Next 7 days</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>Next 14 days</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Next 30 days</option>
                </select>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($checkouts->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No Upcoming Checkouts</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    There are no checkouts scheduled in the next {{ $days }} days.
                </p>
            </div>
        @else
            {{-- Today's Checkouts --}}
            @php $todayCheckouts = $checkouts->filter(fn($e) => $e->isCheckoutToday()); @endphp
            @if($todayCheckouts->count() > 0)
                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800 p-6">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Checkouts Today ({{ $todayCheckouts->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($todayCheckouts as $checkout)
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $checkout->property->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $checkout->summary ?? 'Guest checkout' }}</div>
                                </div>
                                <a href="{{ route('manage.sessions.create') }}?property_id={{ $checkout->property_id }}&date={{ $checkout->end_date->format('Y-m-d') }}" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">
                                    Schedule Cleaning
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tomorrow's Checkouts --}}
            @php $tomorrowCheckouts = $checkouts->filter(fn($e) => $e->isCheckoutTomorrow()); @endphp
            @if($tomorrowCheckouts->count() > 0)
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl border border-orange-200 dark:border-orange-800 p-6">
                    <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-200 mb-4">
                        Checkouts Tomorrow ({{ $tomorrowCheckouts->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($tomorrowCheckouts as $checkout)
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $checkout->property->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $checkout->summary ?? 'Guest checkout' }}</div>
                                </div>
                                <a href="{{ route('manage.sessions.create') }}?property_id={{ $checkout->property_id }}&date={{ $checkout->end_date->format('Y-m-d') }}" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-orange-600 text-white hover:bg-orange-700">
                                    Schedule Cleaning
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Later Checkouts --}}
            @php $laterCheckouts = $checkouts->filter(fn($e) => !$e->isCheckoutToday() && !$e->isCheckoutTomorrow()); @endphp
            @if($laterCheckouts->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Upcoming Checkouts ({{ $laterCheckouts->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($laterCheckouts as $checkout)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $checkout->property->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $checkout->summary ?? 'Guest checkout' }} • {{ $checkout->end_date->format('M j, Y') }}
                                    </div>
                                </div>
                                <a href="{{ route('manage.sessions.create') }}?property_id={{ $checkout->property_id }}&date={{ $checkout->end_date->format('Y-m-d') }}" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Schedule
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
