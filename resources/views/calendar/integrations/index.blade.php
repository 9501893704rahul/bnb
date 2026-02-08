<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                    Calendar Integrations
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $property->name }} - Sync with Airbnb, VRBO, Booking.com
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('properties.edit', $property) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Back to Property
                </a>
                <a href="{{ route('properties.calendar-integrations.create', $property) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Calendar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($integrations->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No Calendar Integrations</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Connect your Airbnb, VRBO, or Booking.com calendar to automatically sync bookings and get checkout alerts.
                </p>
                <a href="{{ route('properties.calendar-integrations.create', $property) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add Your First Calendar
                </a>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Calendar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Platform</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Synced</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($integrations as $integration)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $integration->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $integration->ical_url }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($integration->platform === 'airbnb') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                        @elseif($integration->platform === 'vrbo') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                        @elseif($integration->platform === 'booking') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst($integration->platform) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($integration->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $integration->last_synced_at?->diffForHumans() ?? 'Never' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <form action="{{ route('properties.calendar-integrations.sync', [$property, $integration]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Sync
                                        </button>
                                    </form>
                                    <a href="{{ route('properties.calendar-integrations.edit', [$property, $integration]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Edit
                                    </a>
                                    <form action="{{ route('properties.calendar-integrations.destroy', [$property, $integration]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this calendar?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Upcoming Events Preview --}}
            @php
                $upcomingEvents = $property->calendarEvents()
                    ->where('end_date', '>=', now()->toDateString())
                    ->where('end_date', '<=', now()->addDays(7)->toDateString())
                    ->orderBy('end_date')
                    ->limit(5)
                    ->get();
            @endphp
            @if($upcomingEvents->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Upcoming Checkouts (Next 7 Days)</h3>
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $event->summary ?? 'Booking' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $event->start_date->format('M j') }} - {{ $event->end_date->format('M j, Y') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($event->isCheckoutToday())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            Checkout Today
                                        </span>
                                    @elseif($event->isCheckoutTomorrow())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                                            Checkout Tomorrow
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $event->end_date->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        {{-- Help Section --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">How to Find Your Calendar URL</h3>
            <div class="grid md:grid-cols-3 gap-6 text-sm">
                <div>
                    <div class="font-medium text-blue-800 dark:text-blue-200 mb-2">Airbnb</div>
                    <ol class="text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside">
                        <li>Go to Calendar settings</li>
                        <li>Click "Pricing and availability"</li>
                        <li>Scroll to "Connect to other calendars"</li>
                        <li>Copy the "Export calendar" link</li>
                    </ol>
                </div>
                <div>
                    <div class="font-medium text-blue-800 dark:text-blue-200 mb-2">VRBO</div>
                    <ol class="text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside">
                        <li>Go to your property</li>
                        <li>Click "Calendar"</li>
                        <li>Click "Sync calendars"</li>
                        <li>Copy the iCal export link</li>
                    </ol>
                </div>
                <div>
                    <div class="font-medium text-blue-800 dark:text-blue-200 mb-2">Booking.com</div>
                    <ol class="text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside">
                        <li>Go to Property page</li>
                        <li>Click "Calendar & Pricing"</li>
                        <li>Click "Sync calendars"</li>
                        <li>Copy the export calendar link</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
