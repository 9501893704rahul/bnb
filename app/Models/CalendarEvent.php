<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_integration_id',
        'property_id',
        'uid',
        'summary',
        'description',
        'start_date',
        'end_date',
        'is_checkout_alert_sent',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_checkout_alert_sent' => 'boolean',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(CalendarIntegration::class, 'calendar_integration_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Check if this event is a checkout happening today
     */
    public function isCheckoutToday(): bool
    {
        return $this->end_date->isToday();
    }

    /**
     * Check if this event is a checkout happening tomorrow
     */
    public function isCheckoutTomorrow(): bool
    {
        return $this->end_date->isTomorrow();
    }
}
