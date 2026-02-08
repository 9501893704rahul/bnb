<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CleaningReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'share_token',
        'generated_at',
        'expires_at',
        'view_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_viewed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CleaningSession::class, 'session_id');
    }

    /**
     * Generate a unique share token
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('share_token', $token)->exists());

        return $token;
    }

    /**
     * Get the shareable URL
     */
    public function getShareUrlAttribute(): string
    {
        return route('reports.view', ['token' => $this->share_token]);
    }

    /**
     * Check if the report link has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Record a view
     */
    public function recordView(): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }
}
