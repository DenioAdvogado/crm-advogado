<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaJuridica extends Model
{
    use HasFactory;

    protected $table = 'areas_juridicas';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'nome',
        'pais_aplicavel',
    ];

    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(
            Cliente::class,
            'cliente_area_juridica',
            'area_juridica_id',
            'cliente_id'
        );
    }

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class, 'area_juridica_id');
    }
}
