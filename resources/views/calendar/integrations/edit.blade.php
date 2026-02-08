<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                    Edit Calendar Integration
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $property->name }} - {{ $integration->name }}
                </p>
            </div>
            <a href="{{ route('properties.calendar-integrations.index', $property) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('properties.calendar-integrations.update', [$property, $integration]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Calendar Name
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $integration->name) }}" required
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
                    <option value="airbnb" {{ old('platform', $integration->platform) === 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                    <option value="vrbo" {{ old('platform', $integration->platform) === 'vrbo' ? 'selected' : '' }}>VRBO / HomeAway</option>
                    <option value="booking" {{ old('platform', $integration->platform) === 'booking' ? 'selected' : '' }}>Booking.com</option>
                    <option value="other" {{ old('platform', $integration->platform) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('platform')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ical_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    iCal URL
                </label>
                <input type="url" name="ical_url" id="ical_url" value="{{ old('ical_url', $integration->ical_url) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @error('ical_url')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                    {{ old('is_active', $integration->is_active) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Active (sync this calendar)
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('properties.calendar-integrations.index', $property) }}" 
                    class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
