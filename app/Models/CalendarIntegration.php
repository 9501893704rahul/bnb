<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'platform',
        'ical_url',
        'last_synced_at',
        'is_active',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
