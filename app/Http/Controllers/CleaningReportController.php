<?php

namespace App\Http\Controllers;

use App\Models\CleaningReport;
use App\Models\CleaningSession;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CleaningReportController extends Controller
{
    /**
     * Generate a report for a completed cleaning session
     */
    public function generate(Request $request, CleaningSession $session)
    {
        // Verify session is completed
        if ($session->status !== 'completed') {
            return back()->withErrors(['session' => 'Report can only be generated for completed sessions.']);
        }

        // Check authorization
        $user = $request->user();
        if (!$user->hasRole('admin') && 
            $session->owner_id !== $user->id && 
            $session->property->company_id !== $user->id &&
            $session->property->company_id !== $user->company_id) {
            abort(403);
        }

        // Create or get existing report
        $report = CleaningReport::firstOrCreate(
            ['session_id' => $session->id],
            [
                'share_token' => CleaningReport::generateToken(),
                'generated_at' => now(),
                'expires_at' => now()->addDays(30),
            ]
        );

        return redirect()->route('reports.show', $session)->with('success', 'Report generated!');
    }

    /**
     * Show the report (authenticated view)
     */
    public function show(Request $request, CleaningSession $session)
    {
        $user = $request->user();
        
        // Check authorization
        if (!$user->hasRole('admin') && 
            $session->owner_id !== $user->id && 
            $session->housekeeper_id !== $user->id &&
            $session->property->company_id !== $user->id &&
            $session->property->company_id !== $user->company_id) {
            abort(403);
        }

        $report = CleaningReport::where('session_id', $session->id)->first();
        $data = $this->prepareReportData($session);

        return view('reports.show', array_merge($data, [
            'report' => $report,
            'shareUrl' => $report?->share_url,
        ]));
    }

    /**
     * View report via public share link
     */
    public function viewByToken(string $token)
    {
        $report = CleaningReport::where('share_token', $token)->firstOrFail();

        if ($report->isExpired()) {
            abort(410, 'This report link has expired.');
        }

        $report->recordView();

        $data = $this->prepareReportData($report->session);

        return view('reports.public', array_merge($data, [
            'report' => $report,
        ]));
    }

    /**
     * Send report via email
     */
    public function sendEmail(Request $request, CleaningSession $session)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = CleaningReport::where('session_id', $session->id)->first();
        
        if (!$report) {
            $report = CleaningReport::create([
                'session_id' => $session->id,
                'share_token' => CleaningReport::generateToken(),
                'generated_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);
        }

        // Send email (you'll need to create the mailable)
        Mail::send('emails.cleaning-report', [
            'session' => $session,
            'report' => $report,
            'customMessage' => $request->input('message'),
        ], function ($message) use ($request, $session) {
            $message->to($request->input('email'))
                ->subject('Cleaning Report: ' . $session->property->name . ' - ' . $session->scheduled_date->format('M j, Y'));
        });

        return back()->with('success', 'Report sent to ' . $request->input('email'));
    }

    /**
     * Send report via SMS (requires Twilio or similar)
     */
    public function sendSms(Request $request, CleaningSession $session)
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $report = CleaningReport::where('session_id', $session->id)->first();
        
        if (!$report) {
            $report = CleaningReport::create([
                'session_id' => $session->id,
                'share_token' => CleaningReport::generateToken(),
                'generated_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);
        }

        // For now, just return the link - you can integrate Twilio later
        $message = "Cleaning report for {$session->property->name}: {$report->share_url}";

        // TODO: Integrate with Twilio or other SMS provider
        // Notification::route('vonage', $request->input('phone'))->notify(new CleaningReportSms($report));

        return back()->with('success', 'SMS would be sent to ' . $request->input('phone') . '. SMS integration pending.')
            ->with('share_link', $report->share_url);
    }

    /**
     * Download report photos as ZIP
     */
    public function downloadPhotos(CleaningSession $session)
    {
        $photos = $session->photos()->get();
        
        if ($photos->isEmpty()) {
            return back()->withErrors(['photos' => 'No photos to download.']);
        }

        // Create a temporary ZIP file
        $zipPath = storage_path('app/temp/report_photos_' . $session->id . '.zip');
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['zip' => 'Could not create ZIP file.']);
        }

        foreach ($photos as $photo) {
            $path = $photo->high_res_path ?: $photo->path;
            $fullPath = storage_path('app/public/' . $path);
            
            if (file_exists($fullPath)) {
                $room = $photo->room;
                $filename = ($room ? $room->name . '_' : '') . 'photo_' . $photo->id . '.' . pathinfo($path, PATHINFO_EXTENSION);
                $zip->addFile($fullPath, $filename);
            }
        }

        $zip->close();

        return response()->download($zipPath, 'cleaning_report_' . $session->scheduled_date->format('Y-m-d') . '.zip')
            ->deleteFileAfterSend(true);
    }

    /**
     * Prepare report data for views
     */
    protected function prepareReportData(CleaningSession $session): array
    {
        $session->load([
            'property.rooms',
            'housekeeper',
            'owner',
            'checklistItems.task',
            'checklistItems.photos',
            'photos.room',
        ]);

        // Group checklist items by room
        $itemsByRoom = $session->checklistItems->groupBy('room_id');
        
        // Get property-level tasks (room_id is null)
        $propertyLevelItems = $itemsByRoom->get(null, collect());
        
        // Get room-level items
        $roomItems = $itemsByRoom->except([null]);

        // Group photos by room
        $photosByRoom = $session->photos->groupBy('room_id');

        // Separate problem photos
        $problemPhotos = $session->photos->where('photo_type', 'problem');
        $completionPhotos = $session->photos->where('photo_type', 'completion');

        // Get notes from checklist items
        $notes = $session->checklistItems->filter(fn($item) => !empty($item->note));

        // Calculate statistics
        $totalTasks = $session->checklistItems->count();
        $completedTasks = $session->checklistItems->where('checked', true)->count();
        $totalPhotos = $session->photos->count();

        return [
            'session' => $session,
            'property' => $session->property,
            'housekeeper' => $session->housekeeper,
            'owner' => $session->owner,
            'rooms' => $session->property->rooms,
            'propertyLevelItems' => $propertyLevelItems,
            'roomItems' => $roomItems,
            'photosByRoom' => $photosByRoom,
            'problemPhotos' => $problemPhotos,
            'completionPhotos' => $completionPhotos,
            'notes' => $notes,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'totalPhotos' => $totalPhotos,
            'completionRate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
        ];
    }
}
