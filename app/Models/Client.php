<?php

namespace App\Models;

use App\Notifications\Portal\ResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'person_type',
        'country',
        'document_number',
        'secondary_document_number',
        'phone',
        'email',
        'address_street',
        'address_city',
        'address_state',
        'address_zipcode',
        'address_country',
        'portal_password',
        'active',
    ];

    protected $hidden = [
        'portal_password',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * O login do portal usa o campo "portal_password" em vez do "password" padrão do
     * Eloquent, já que esta tabela também guarda dados de negócio (não é uma tabela de auth
     * dedicada). Por isso sobrescrevemos getAuthPassword().
     */
    public function getAuthPassword(): string
    {
        return $this->portal_password;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function legalAreas(): BelongsToMany
    {
        return $this->belongsToMany(
            LegalArea::class,
            'client_legal_area',
            'client_id',
            'legal_area_id'
        );
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'client_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'client_id');
    }

    public function financialEntries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class, 'client_id');
    }
}
