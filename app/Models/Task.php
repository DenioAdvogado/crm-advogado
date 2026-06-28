<?php

namespace App\Models;

use App\Jobs\DeleteTaskGoogleCalendarEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'case_id',
        'responsible_id',
        'title',
        'description',
        'due_date',
        'status',
    ];

    /**
     * Bloco 7: tarefa excluída (soft delete) não deve continuar aparecendo na agenda do
     * responsável. Feito como model event (não no controller) porque hoje não existe rota
     * de exclusão de tarefas, mas se uma for adicionada depois, a sincronização já funciona
     * automaticamente sem precisar lembrar de chamar isso de novo.
     */
    protected static function booted(): void
    {
        static::deleted(function (Task $task) {
            DeleteTaskGoogleCalendarEvent::dispatch($task->id);
        });
    }

    /**
     * Filtra a query conforme a visibilidade de tarefas do Bloco 4 (mesma regra da
     * TaskPolicy::view, aplicada em lote para a listagem): administrador vê tudo; advogado
     * vê as próprias ou todas se can_view_all_cases; funcionário só vê as próprias.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdministrator()) {
            return $query;
        }

        if ($user->isLawyer() && $user->can_view_all_cases) {
            return $query;
        }

        return $query->where('responsible_id', $user->id);
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Processo relacionado a esta tarefa, vindo direto de "case_id" ou, se a tarefa estiver
     * vinculada a um serviço em vez de um processo, do "case_id" desse serviço. Usado ao
     * concluir a tarefa para saber em qual processo registrar a atualização (Bloco 4).
     */
    public function relatedCase(): ?LegalCase
    {
        if ($this->case_id) {
            return $this->case;
        }

        return $this->service?->case;
    }
}
