<?php

namespace App\Services;

use App\Models\CalendarIntegration;
use App\Models\CalendarEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendarSyncService
{
    /**
     * Sync a single calendar integration
     */
    public function sync(CalendarIntegration $integration): array
    {
        $results = [
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'errors' => [],
        ];

        try {
            $response = Http::timeout(30)->get($integration->ical_url);
            
            if (!$response->successful()) {
                $results['errors'][] = 'Failed to fetch calendar: HTTP ' . $response->status();
                return $results;
            }

            $icalData = $response->body();
            $events = $this->parseIcal($icalData);

            // Get existing events for this integration
            $existingEvents = $integration->events()->pluck('id', 'uid')->toArray();
            $processedUids = [];

            foreach ($events as $event) {
                if (empty($event['uid'])) {
                    continue;
                }

                $processedUids[] = $event['uid'];

                $existingEvent = CalendarEvent::where('calendar_integration_id', $integration->id)
                    ->where('uid', $event['uid'])
                    ->first();

                if ($existingEvent) {
                    $existingEvent->update([
                        'summary' => $event['summary'] ?? null,
                        'description' => $event['description'] ?? null,
                        'start_date' => $event['start_date'],
                        'end_date' => $event['end_date'],
                    ]);
                    $results['updated']++;
                } else {
                    CalendarEvent::create([
                        'calendar_integration_id' => $integration->id,
                        'property_id' => $integration->property_id,
                        'uid' => $event['uid'],
                        'summary' => $event['summary'] ?? null,
                        'description' => $event['description'] ?? null,
                        'start_date' => $event['start_date'],
                        'end_date' => $event['end_date'],
                    ]);
                    $results['created']++;
                }
            }

            // Remove events that no longer exist in the calendar
            $toDelete = array_diff(array_keys($existingEvents), $processedUids);
            if (!empty($toDelete)) {
                CalendarEvent::where('calendar_integration_id', $integration->id)
                    ->whereIn('uid', $toDelete)
                    ->delete();
                $results['deleted'] = count($toDelete);
            }

            $integration->update(['last_synced_at' => now()]);

        } catch (\Exception $e) {
            Log::error('Calendar sync failed', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage(),
            ]);
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Sync all active integrations
     */
    public function syncAll(): array
    {
        $integrations = CalendarIntegration::where('is_active', true)->get();
        $allResults = [];

        foreach ($integrations as $integration) {
            $allResults[$integration->id] = $this->sync($integration);
        }

        return $allResults;
    }

    /**
     * Parse iCal data into events array
     */
    protected function parseIcal(string $icalData): array
    {
        $events = [];
        $lines = explode("\n", str_replace("\r\n", "\n", $icalData));
        
        $currentEvent = null;
        $currentKey = null;
        $currentValue = '';

        foreach ($lines as $line) {
            $line = rtrim($line);
            
            // Handle line folding (lines starting with space/tab are continuations)
            if (preg_match('/^[\s\t]/', $line)) {
                $currentValue .= ltrim($line);
                continue;
            }
            
            // Process the previous complete line
            if ($currentKey !== null && $currentEvent !== null) {
                $this->processIcalLine($currentEvent, $currentKey, $currentValue);
            }

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent !== null) {
                if (isset($currentEvent['start_date']) && isset($currentEvent['end_date'])) {
                    $events[] = $currentEvent;
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null && strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                // Handle parameters like DTSTART;VALUE=DATE:20240101
                $key = explode(';', $key)[0];
                $currentKey = $key;
                $currentValue = $value;
            }
        }

        return $events;
    }

    /**
     * Process a single iCal line into event array
     */
    protected function processIcalLine(array &$event, string $key, string $value): void
    {
        switch ($key) {
            case 'UID':
                $event['uid'] = $value;
                break;
            case 'SUMMARY':
                $event['summary'] = $this->decodeIcalText($value);
                break;
            case 'DESCRIPTION':
                $event['description'] = $this->decodeIcalText($value);
                break;
            case 'DTSTART':
                $event['start_date'] = $this->parseIcalDate($value);
                break;
            case 'DTEND':
                $event['end_date'] = $this->parseIcalDate($value);
                break;
        }
    }

    /**
     * Parse iCal date format
     */
    protected function parseIcalDate(string $value): ?string
    {
        // Handle date-only format (YYYYMMDD)
        if (preg_match('/^(\d{8})$/', $value, $matches)) {
            return Carbon::createFromFormat('Ymd', $matches[1])->toDateString();
        }
        
        // Handle datetime format (YYYYMMDDTHHmmssZ or YYYYMMDDTHHmmss)
        if (preg_match('/^(\d{8}T\d{6})Z?$/', $value, $matches)) {
            return Carbon::createFromFormat('Ymd\THis', $matches[1])->toDateString();
        }

        return null;
    }

    /**
     * Decode iCal text (handle escape sequences)
     */
    protected function decodeIcalText(string $text): string
    {
        $text = str_replace(['\\n', '\\N'], "\n", $text);
        $text = str_replace('\\,', ',', $text);
        $text = str_replace('\\;', ';', $text);
        $text = str_replace('\\\\', '\\', $text);
        return $text;
    }

    /**
     * Get upcoming checkouts across all properties for a user
     */
    public function getUpcomingCheckoutsForUser($user, int $days = 7): \Illuminate\Support\Collection
    {
        $query = CalendarEvent::query()
            ->where('end_date', '>=', now()->toDateString())
            ->where('end_date', '<=', now()->addDays($days)->toDateString())
            ->with(['property', 'integration'])
            ->orderBy('end_date');

        // Filter based on user role
        if ($user->hasRole('admin')) {
            // Admin sees all
        } elseif ($user->hasRole('company')) {
            $query->whereHas('property', function ($q) use ($user) {
                $q->where('company_id', $user->id);
            });
        } elseif ($user->hasRole('owner')) {
            $query->whereHas('property', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhere('company_id', $user->company_id);
            });
        }

        return $query->get();
    }
}
