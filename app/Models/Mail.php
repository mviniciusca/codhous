<?php

namespace App\Models;

use App\Models\Scopes\UserMailScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Mail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_sent' => 'boolean',
            'is_read' => 'boolean',
            'is_favorite' => 'boolean',
            'is_spam' => 'boolean',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserMailScope);

        // Automatically set user_id when creating
        static::creating(function ($mail) {
            if (! $mail->user_id && Auth::check()) {
                $mail->user_id = Auth::id();
            }
        });
    }

    /**
     * Get the user who sent/received this mail.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trash()
    {
        return $this->withTrashed();
    }
}
