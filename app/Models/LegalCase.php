<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Mesma regra da LegalCasePolicy::view, aplicada em lote para listagens (Bloco 9):
     * administrador e funcionário veem todos; advogado só os seus, a menos que
     * can_view_all_cases esteja liberado.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdministrator() || $user->isStaff()) {
            return $query;
        }

        if ($user->isLawyer() && $user->can_view_all_cases) {
            return $query;
        }

        return $query->where('responsible_lawyer_id', $user->id);
    }

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

    public function updates(): HasMany
    {
        return $this->hasMany(CaseUpdate::class, 'case_id')->latest();
    }
}
