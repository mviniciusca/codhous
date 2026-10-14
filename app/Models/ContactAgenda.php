<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactAgenda extends Model
{
    use HasFactory;

    protected $table = 'contact_agenda';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'position',
        'category',
        'status',
        'source',
        'notes',
        'is_favorite',
        'last_contact_at',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'last_contact_at' => 'datetime',
    ];

    public const CATEGORY_CLIENT = 'client';

    public const CATEGORY_LEAD = 'lead';

    public const CATEGORY_SUPPLIER = 'supplier';

    public const CATEGORY_PARTNER = 'partner';

    public const CATEGORY_CONTACT = 'contact';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_IN_CONTACT = 'in_contact';

    public const STATUS_PENDING = 'pending';

    public const STATUS_NO_RESPONSE = 'no_response';

    public const STATUS_IN_NEGOTIATION = 'in_negotiation';

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_CLIENT => 'Cliente',
            self::CATEGORY_LEAD => 'Lead',
            self::CATEGORY_SUPPLIER => 'Fornecedor',
            self::CATEGORY_PARTNER => 'Parceiro',
            self::CATEGORY_CONTACT => 'Geral',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_IN_CONTACT => 'Em contato',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_NO_RESPONSE => 'Sem retorno',
            self::STATUS_IN_NEGOTIATION => 'Em negociação',
        ];
    }

    public static function sourceLabels(): array
    {
        return [
            'website' => 'Site / Portal',
            'manual' => 'Cadastro Manual',
            'referral' => 'Indicação',
            'budget' => 'Formulário de Orçamento',
            'other' => 'Outro',
        ];
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(ContactInteraction::class, 'contact_agenda_id');
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        if (empty($words)) {
            return '??';
        }

        if (count($words) === 1) {
            return mb_strtoupper(mb_substr($words[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr(end($words), 0, 1));
    }

    public function getDisplayPhoneAttribute(): ?string
    {
        return $this->phone;
    }

    public function getWaLinkAttribute(): ?string
    {
        if (! $this->phone) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $this->phone);
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            $digits = '55'.$digits;
        }

        return 'https://wa.me/'.$digits;
    }
}
