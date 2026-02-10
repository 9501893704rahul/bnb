@php
    $upcomingSessions = $sessions->filter(fn($s) => $s->scheduled_date->isFuture() || $s->scheduled_date->isToday());
    $pastSessions = $sessions->filter(fn($s) => $s->scheduled_date->isPast() && !$s->scheduled_date->isToday());
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold">My Assignments</h2>
    </x-slot>

    {{-- Upcoming Sessions Section --}}
    @if($upcomingSessions->count() > 0)
        <div class="mb-6">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Upcoming Assignments
            </h3>

            {{-- Mobile Card View --}}
            <div class="md:hidden space-y-3">
                @foreach($upcomingSessions as $s)
                    <a href="{{ route('sessions.show', $s) }}" class="block">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 {{ $s->scheduled_date->isToday() ? 'border-green-500 dark:border-green-600' : 'border-gray-200 dark:border-gray-700' }} p-4 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                            @if($s->scheduled_date->isToday())
                                <div class="mb-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Today
                                    </span>
                                </div>
                            @endif
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $s->property->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $s->scheduled_date->isToday() ? 'Today' : $s->scheduled_date->toFormattedDateString() }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <x-status-badge :status="$s->status" />
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-end">
                                <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium flex items-center gap-1">
                                    {{ $s->status === 'pending' ? 'Start' : 'Continue' }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Desktop Table View --}}
            <x-card class="!px-0 hidden md:block">
                <table class="min-w-full text-sm">
                    <thead class="uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Property</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 w-32"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach($upcomingSessions as $s)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $s->scheduled_date->isToday() ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                <td class="px-4 py-3">
                                    @if($s->scheduled_date->isToday())
                                        <span class="font-semibold text-green-700 dark:text-green-400">Today</span>
                                    @else
                                        {{ $s->scheduled_date->toFormattedDateString() }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $s->property->name }}</td>
                                <td class="px-4 py-3 text-center"><x-status-badge :status="$s->status" /></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('sessions.show', $s) }}" class="text-indigo-600 hover:underline font-medium">
                                        {{ $s->status === 'pending' ? 'Start' : 'Continue' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    @endif

    {{-- Past Sessions Section --}}
    @if($pastSessions->count() > 0)
        <div class="mb-6">
            <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Past Assignments
            </h3>

            {{-- Mobile Card View --}}
            <div class="md:hidden space-y-3">
                @foreach($pastSessions as $s)
                    <a href="{{ route('sessions.show', $s) }}" class="block">
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4 opacity-75 hover:opacity-100 transition-opacity">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-700 dark:text-gray-300 truncate">
                                        {{ $s->property->name }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                        {{ $s->scheduled_date->toFormattedDateString() }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <x-status-badge :status="$s->status" />
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-end">
                                <span class="text-sm text-gray-500 dark:text-gray-500 font-medium flex items-center gap-1">
                                    View
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Desktop Table View --}}
            <x-card class="!px-0 hidden md:block">
                <table class="min-w-full text-sm">
                    <thead class="uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Property</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 w-32"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach($pastSessions as $s)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 opacity-75 hover:opacity-100">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $s->scheduled_date->toFormattedDateString() }}</td>
                                <td class="px-4 py-3 font-medium text-gray-600 dark:text-gray-400">{{ $s->property->name }}</td>
                                <td class="px-4 py-3 text-center"><x-status-badge :status="$s->status" /></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('sessions.show', $s) }}" class="text-gray-500 hover:text-indigo-600 hover:underline font-medium">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    @endif

    @if($sessions->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No assignments yet</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Check back later for new cleaning sessions.</p>
        </div>
    @endif

    {{ $sessions->links() }}
</x-app-layout>
