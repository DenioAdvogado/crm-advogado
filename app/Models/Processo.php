<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Processo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'processos';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'excluido_em';

    protected $fillable = [
        'cliente_id',
        'advogado_responsavel_id',
        'numero_processo',
        'area_juridica_id',
        'pais',
        'status',
        'data_abertura',
        'prazo_atual',
        'descricao',
    ];

    protected function casts(): array
    {
        return [
            'data_abertura' => 'date',
            'prazo_atual' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function advogadoResponsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'advogado_responsavel_id');
    }

    public function areaJuridica(): BelongsTo
    {
        return $this->belongsTo(AreaJuridica::class, 'area_juridica_id');
    }

    public function servicos(): HasMany
    {
        return $this->hasMany(Servico::class, 'processo_id');
    }

    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'processo_id');
    }

    public function lancamentosFinanceiros(): HasMany
    {
        return $this->hasMany(FinanceiroLancamento::class, 'processo_id');
    }
}
