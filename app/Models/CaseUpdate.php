<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CaseUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'description',
        'author_id',
        'notify_client',
    ];

    protected function casts(): array
    {
        return [
            'notify_client' => 'boolean',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function emailLog(): HasOne
    {
        return $this->hasOne(EmailLog::class, 'case_update_id');
    }
}
