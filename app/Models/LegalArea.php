<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'applicable_country',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'client_legal_area',
            'legal_area_id',
            'client_id'
        );
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'legal_area_id');
    }
}
