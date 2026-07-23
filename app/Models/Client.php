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
        'nationality',
        'marital_status',
        'stable_union',
        'profession',
        'birth_date',
        'document_issuer',
        'mother_name',
        'father_name',
        'phone',
        'email',
        'address_street',
        'address_number',
        'address_complement',
        'address_neighborhood',
        'address_city',
        'address_state',
        'address_zipcode',
        'address_country',
        'company_legal_name',
        'company_trade_name',
        'legal_representative',
        'legal_representative_document',
        'legal_representative_role',
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
            'stable_union' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    /**
     * O login do portal usa o campo "portal_password" em vez do "password" padrÃ£o do
     * Eloquent, jÃ¡ que esta tabela tambÃ©m guarda dados de negÃ³cio (nÃ£o Ã© uma tabela de auth
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

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'client_id');
    }

    /**
     * Formata document_number conforme paÃ­s/tipo de pessoa (Bloco 3):
     * - Brasil + individual: CPF (000.000.000-00)
     * - Brasil + company: CNPJ (00.000.000/0000-00)
     * - Portugal (individual ou company): NIF (000000000, sem pontuaÃ§Ã£o)
     * Se o nÃºmero de dÃ­gitos nÃ£o bater com o esperado, devolve o valor original sem
     * mÃ¡scara, para nÃ£o exibir algo enganoso com dados de teste/incompletos.
     */
    public function getFormattedDocumentNumberAttribute(): string
    {
        $digits = preg_replace('/\D/', '', (string) $this->document_number);

        if ($this->country === 'Brazil' && $this->person_type === 'individual' && strlen($digits) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
        }

        if ($this->country === 'Brazil' && $this->person_type === 'company' && strlen($digits) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
        }

        if ($this->country === 'Portugal' && strlen($digits) === 9) {
            return $digits;
        }

        return $this->document_number;
    }
}
