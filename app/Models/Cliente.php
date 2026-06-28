<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'excluido_em';

    protected $fillable = [
        'nome',
        'tipo_pessoa',
        'pais',
        'documento',
        'documento_secundario',
        'telefone',
        'email',
        'endereco_logradouro',
        'endereco_cidade',
        'endereco_estado',
        'endereco_cep',
        'endereco_pais',
        'senha_acesso',
        'ativo',
    ];

    protected $hidden = [
        'senha_acesso',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function areasJuridicas(): BelongsToMany
    {
        return $this->belongsToMany(
            AreaJuridica::class,
            'cliente_area_juridica',
            'cliente_id',
            'area_juridica_id'
        );
    }

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class, 'cliente_id');
    }

    public function servicos(): HasMany
    {
        return $this->hasMany(Servico::class, 'cliente_id');
    }

    public function lancamentosFinanceiros(): HasMany
    {
        return $this->hasMany(FinanceiroLancamento::class, 'cliente_id');
    }
}
