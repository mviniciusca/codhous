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
            self::CATEGORY_CONTACT => __('Contact'),
            self::CATEGORY_LEAD => __('Lead'),
            self::CATEGORY_CLIENT => __('Client'),
            self::CATEGORY_SUPPLIER => __('Supplier'),
            self::CATEGORY_PARTNER => __('Partner'),
        ];
    }

    public static function sourceLabels(): array
    {
        return [
            'website' => __('Website'),
            'manual' => __('Manual'),
            'referral' => __('Referral'),
            'budget' => __('Budget form'),
            'other' => __('Other'),
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
