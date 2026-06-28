<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

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
