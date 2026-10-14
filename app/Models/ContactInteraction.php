<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInteraction extends Model
{
    use HasFactory;

    protected $table = 'contact_interactions';

    protected $fillable = [
        'contact_agenda_id',
        'user_id',
        'title',
        'type',
        'scheduled_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public const TYPE_MEETING = 'meeting';

    public const TYPE_CALL = 'call';

    public const TYPE_EMAIL = 'email';

    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_MEETING => 'Reunião',
            self::TYPE_CALL => 'Ligação',
            self::TYPE_EMAIL => 'E-mail',
            self::TYPE_OTHER => 'Outro',
        ];
    }

    public static function typeColors(): array
    {
        return [
            self::TYPE_MEETING => '#3b82f6',
            self::TYPE_CALL => '#10b981',
            self::TYPE_EMAIL => '#f59e0b',
            self::TYPE_OTHER => '#a855f7',
        ];
    }

    public static function typeIcons(): array
    {
        return [
            self::TYPE_MEETING => 'heroicon-m-users',
            self::TYPE_CALL => 'heroicon-m-phone',
            self::TYPE_EMAIL => 'heroicon-m-envelope',
            self::TYPE_OTHER => 'heroicon-m-calendar',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(ContactAgenda::class, 'contact_agenda_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
