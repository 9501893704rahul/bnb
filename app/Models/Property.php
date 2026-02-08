<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'company_id',
        'name',
        'address',
        'photo_path',
        'beds',
        'baths',
        'latitude',
        'longitude',
        'geo_radius_m'
    ];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'property_room')
            ->withTimestamps()
            ->withPivot(['sort_order'])
            ->orderBy('property_room.sort_order');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'company_id', 'id');
    }

    public function propertyTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'property_tasks')
            ->withTimestamps()
            ->withPivot(['sort_order', 'instructions', 'visible_to_owner', 'visible_to_housekeeper'])
            ->orderBy('property_tasks.sort_order');
    }

    public function calendarIntegrations(): HasMany
    {
        return $this->hasMany(CalendarIntegration::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function cleaningSessions(): HasMany
    {
        return $this->hasMany(CleaningSession::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo_path
            ? (str_starts_with($this->photo_path, 'http') ? $this->photo_path : asset('storage/' . $this->photo_path))
            : asset('images/placeholders/property.png');
    }

    /**
     * Get upcoming checkouts from calendar integrations
     */
    public function getUpcomingCheckouts(int $days = 7)
    {
        return $this->calendarEvents()
            ->where('end_date', '>=', now()->toDateString())
            ->where('end_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('end_date')
            ->get();
    }
}
