<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'source',
        'notes',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    public const CATEGORY_CONTACT = 'contact';
    public const CATEGORY_LEAD = 'lead';
    public const CATEGORY_CLIENT = 'client';
    public const CATEGORY_SUPPLIER = 'supplier';
    public const CATEGORY_PARTNER = 'partner';

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_CONTACT => 'Contato Geral',
            self::CATEGORY_LEAD => 'Lead / Interesse',
            self::CATEGORY_CLIENT => 'Cliente',
            self::CATEGORY_SUPPLIER => 'Fornecedor',
            self::CATEGORY_PARTNER => 'Parceiro / Colaborador',
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
            $digits = '55' . $digits;
        }

        return 'https://wa.me/' . $digits;
    }
}
