<?php

namespace App\Http\Controllers;

use App\Models\CleaningSession;
use App\Models\RoomPhoto;
use App\Services\ImageTimestampService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(Request $request, CleaningSession $session, $roomId)
    {
        $request->validate([
            'photos.*' => ['required', 'image', 'max:10240'], // 10MB per image for high-res
            'photo_type' => ['nullable', 'in:completion,problem'],
        ]);

        $room = $session->property->rooms()->findOrFail($roomId);
        $saved = [];
        $photoType = $request->input('photo_type', 'completion');
        $capturedAt = now();
        
        // Get files from request - ensure we only process unique files
        $files = $request->file('photos', []);
        
        // Remove duplicates by comparing file content hash
        $processedHashes = [];
        foreach ($files as $file) {
            $fileHash = md5_file($file->getRealPath());
            
            if (in_array($fileHash, $processedHashes)) {
                continue;
            }
            
            $processedHashes[] = $fileHash;
            
            // Store original high-res image
            $highResPath = $file->store('room_photos/high_res', 'public');
            
            // Store a copy for processing
            $webPath = $file->store('room_photos', 'public');
            
            // Generate thumbnail with timestamp overlay
            $thumbnailPath = ImageTimestampService::overlayAndSave($webPath, $capturedAt);
            
            $photo = $session->photos()->create([
                'room_id' => $room->id,
                'path' => $webPath,
                'high_res_path' => $highResPath,
                'thumbnail_path' => $thumbnailPath,
                'photo_type' => $photoType,
                'captured_at' => $capturedAt,
                'has_timestamp_overlay' => $thumbnailPath !== null,
            ]);
            
            $saved[] = [
                'id' => $photo->id,
                'url' => $photo->url,
                'high_res_url' => $photo->high_res_url,
                'captured_at' => $photo->captured_at->format('H:i'),
                'photo_type' => $photo->photo_type,
            ];
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($saved) . ' photos uploaded.',
                'photos' => $saved,
            ]);
        }

        return back()->with('ok', count($saved) . ' photos uploaded.');
    }

    public function destroy(CleaningSession $session, RoomPhoto $photo)
    {
        if ($photo->session_id !== $session->id) {
            return response()->json(['success' => false, 'message' => 'Photo not found'], 404);
        }

        // Delete all associated files
        $pathsToDelete = array_filter([
            $photo->path,
            $photo->high_res_path,
            $photo->thumbnail_path,
        ]);

        foreach ($pathsToDelete as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully',
        ]);
    }

    /**
     * Download high-resolution photo
     */
    public function download(CleaningSession $session, RoomPhoto $photo)
    {
        if ($photo->session_id !== $session->id) {
            abort(404);
        }

        $path = $photo->high_res_path ?: $photo->path;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path, 'photo_' . $photo->id . '_highres.' . pathinfo($path, PATHINFO_EXTENSION));
    }
}
