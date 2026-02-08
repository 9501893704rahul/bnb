<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'profile_photo_path',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return str_starts_with($this->profile_photo_path, 'http')
                ? $this->profile_photo_path
                : asset('storage/' . $this->profile_photo_path);
        }

        // Get theme color from settings and remove '#' for API
        $themeColor = Setting::get('theme_color', '#842eb8');
        $backgroundColor = str_replace('#', '', $themeColor);

        // Return a default avatar placeholder
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=' . $backgroundColor . '&color=fff&size=128';
    }

    /**
     * Get the company that this user belongs to (for housekeepers/owners under a company).
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    /**
     * Get all users (employees/housekeepers/owners) under this company.
     */
    public function companyMembers(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * Get all housekeepers under this company.
     */
    public function companyHousekeepers(): HasMany
    {
        return $this->hasMany(User::class, 'company_id')
            ->whereHas('roles', fn($q) => $q->where('name', 'housekeeper'));
    }

    /**
     * Get all owners under this company.
     */
    public function companyOwners(): HasMany
    {
        return $this->hasMany(User::class, 'company_id')
            ->whereHas('roles', fn($q) => $q->where('name', 'owner'));
    }

    /**
     * Get properties owned by this user.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    /**
     * Get properties managed by this company.
     */
    public function companyProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'company_id');
    }

    /**
     * Check if user is a company.
     */
    public function isCompany(): bool
    {
        return $this->hasRole('company');
    }

    /**
     * Get effective company ID (either own ID if company, or company_id if member).
     */
    public function getEffectiveCompanyId(): ?int
    {
        if ($this->isCompany()) {
            return $this->id;
        }
        return $this->company_id;
    }
}
