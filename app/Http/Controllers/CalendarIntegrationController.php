<?php

namespace App\Http\Controllers;

use App\Models\CalendarIntegration;
use App\Models\Property;
use App\Services\CalendarSyncService;
use Illuminate\Http\Request;

class CalendarIntegrationController extends Controller
{
    protected CalendarSyncService $syncService;

    public function __construct(CalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * List calendar integrations for a property
     */
    public function index(Property $property)
    {
        $this->authorize('view', $property);

        $integrations = $property->calendarIntegrations()
            ->orderBy('name')
            ->get();

        return view('calendar.integrations.index', compact('property', 'integrations'));
    }

    /**
     * Show form to create new integration
     */
    public function create(Property $property)
    {
        $this->authorize('update', $property);

        return view('calendar.integrations.create', compact('property'));
    }

    /**
     * Store new calendar integration
     */
    public function store(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:airbnb,vrbo,booking,other'],
            'ical_url' => ['required', 'url', 'max:2048'],
        ]);

        $integration = $property->calendarIntegrations()->create($validated);

        // Sync immediately
        $results = $this->syncService->sync($integration);

        $message = "Calendar added. Synced {$results['created']} events.";
        if (!empty($results['errors'])) {
            $message .= ' Warning: ' . implode(', ', $results['errors']);
        }

        return redirect()
            ->route('properties.calendar-integrations.index', $property)
            ->with('success', $message);
    }

    /**
     * Show form to edit integration
     */
    public function edit(Property $property, CalendarIntegration $integration)
    {
        $this->authorize('update', $property);

        return view('calendar.integrations.edit', compact('property', 'integration'));
    }

    /**
     * Update calendar integration
     */
    public function update(Request $request, Property $property, CalendarIntegration $integration)
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:airbnb,vrbo,booking,other'],
            'ical_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $integration->update($validated);

        return redirect()
            ->route('properties.calendar-integrations.index', $property)
            ->with('success', 'Calendar updated.');
    }

    /**
     * Delete calendar integration
     */
    public function destroy(Property $property, CalendarIntegration $integration)
    {
        $this->authorize('update', $property);

        $integration->delete();

        return redirect()
            ->route('properties.calendar-integrations.index', $property)
            ->with('success', 'Calendar removed.');
    }

    /**
     * Manually sync a calendar
     */
    public function sync(Property $property, CalendarIntegration $integration)
    {
        $this->authorize('update', $property);

        $results = $this->syncService->sync($integration);

        $message = "Sync complete. Created: {$results['created']}, Updated: {$results['updated']}, Deleted: {$results['deleted']}.";
        if (!empty($results['errors'])) {
            $message .= ' Errors: ' . implode(', ', $results['errors']);
        }

        return back()->with('success', $message);
    }

    /**
     * Show upcoming checkouts
     */
    public function checkouts(Request $request)
    {
        $user = $request->user();
        $days = (int) $request->get('days', 7);
        
        $checkouts = $this->syncService->getUpcomingCheckoutsForUser($user, $days);

        return view('calendar.checkouts', compact('checkouts', 'days'));
    }
}
