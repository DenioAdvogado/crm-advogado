<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [
        'client_id',
        'responsible_lawyer_id',
        'case_number',
        'legal_area_id',
        'country',
        'status',
        'opened_at',
        'current_deadline',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'date',
            'current_deadline' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function responsibleLawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_lawyer_id');
    }

    public function legalArea(): BelongsTo
    {
        return $this->belongsTo(LegalArea::class, 'legal_area_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'case_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'case_id');
    }

    public function financialEntries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class, 'case_id');
    }
}
