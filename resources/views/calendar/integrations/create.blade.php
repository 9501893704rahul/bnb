<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                    Add Calendar Integration
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $property->name }}
                </p>
            </div>
            <a href="{{ route('properties.calendar-integrations.index', $property) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('properties.calendar-integrations.store', $property) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Calendar Name
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="e.g., Airbnb Calendar, VRBO Sync"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="platform" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Platform
                </label>
                <select name="platform" id="platform" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Select platform...</option>
                    <option value="airbnb" {{ old('platform') === 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                    <option value="vrbo" {{ old('platform') === 'vrbo' ? 'selected' : '' }}>VRBO / HomeAway</option>
                    <option value="booking" {{ old('platform') === 'booking' ? 'selected' : '' }}>Booking.com</option>
                    <option value="other" {{ old('platform') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('platform')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ical_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    iCal URL
                </label>
                <input type="url" name="ical_url" id="ical_url" value="{{ old('ical_url') }}" required
                    placeholder="https://www.airbnb.com/calendar/ical/..."
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @error('ical_url')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Paste the calendar export URL from your booking platform.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('properties.calendar-integrations.index', $property) }}" 
                    class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700">
                    Add Calendar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
