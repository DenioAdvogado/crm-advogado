<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'access_level', 'active', 'can_view_all_cases', 'can_access_financial'])]
#[Hidden(['password', 'remember_token', 'google_access_token', 'google_refresh_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
            'active' => 'boolean',
            'can_view_all_cases' => 'boolean',
            'can_access_financial' => 'boolean',
            // Tokens do Google Calendar (Bloco 7) criptografados em repouso.
            'google_access_token' => 'encrypted',
            'google_refresh_token' => 'encrypted',
            'google_token_expires_at' => 'datetime',
            'google_calendar_connected_at' => 'datetime',
        ];
    }

    public function isGoogleCalendarConnected(): bool
    {
        return ! empty($this->google_refresh_token);
    }

    public function isAdministrator(): bool
    {
        return $this->access_level === 'administrator';
    }

    public function isLawyer(): bool
    {
        return $this->access_level === 'lawyer';
    }

    public function isStaff(): bool
    {
        return $this->access_level === 'staff';
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'responsible_lawyer_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'responsible_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'responsible_id');
    }
}
