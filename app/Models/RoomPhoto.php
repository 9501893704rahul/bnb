<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RoomPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'room_id',
        'path',
        'high_res_path',
        'thumbnail_path',
        'photo_type',
        'captured_at',
        'has_timestamp_overlay'
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'has_timestamp_overlay' => 'bool'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CleaningSession::class, 'session_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get URL for web display (with timestamp overlay)
     */
    public function getUrlAttribute()
    {
        $path = $this->thumbnail_path ?: $this->path;
        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/' . $path);
    }

    /**
     * Get URL for high-resolution download
     */
    public function getHighResUrlAttribute()
    {
        $path = $this->high_res_path ?: $this->path;
        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/' . $path);
    }

    /**
     * Check if this is a completion photo
     */
    public function isCompletionPhoto(): bool
    {
        return $this->photo_type === 'completion';
    }

    /**
     * Check if this is a problem photo
     */
    public function isProblemPhoto(): bool
    {
        return $this->photo_type === 'problem';
    }
}
