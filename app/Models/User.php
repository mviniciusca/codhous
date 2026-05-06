<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\BudgetHistory;
use App\Models\UserSetting;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasAvatar
{
    use HasFactory, Notifiable, \Spatie\Permission\Traits\HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function budgetHistory()
    {
        return $this->hasMany(BudgetHistory::class);
    }

    /**
     * Get all budgets created by this user.
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Get all mails sent/received by this user.
     */
    public function mails()
    {
        return $this->hasMany(Mail::class);
    }

    /**
     * Get all activities performed by this user.
     */
    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'causer')
            ->orderBy('created_at', 'desc');
    }
}
