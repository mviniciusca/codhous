<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AccessArea;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, \Spatie\Permission\Traits\HasRoles;

    /**
     * The attributes that are mass assignable.
     * is_active e perfis ficam de fora de propósito: só mudam por ações autorizadas.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    /** Mesmo default da coluna, para a instância recém-criada já refletir o banco */
    protected $attributes = [
        'is_active' => true,
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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Usuário desativado não entra no painel (vale também para sessões já abertas:
     * o Filament confere isso a cada requisição).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function canAccessArea(AccessArea|string $area): bool
    {
        $area = $area instanceof AccessArea ? $area : AccessArea::from($area);

        return (bool) $this->is_active && $this->checkPermissionTo($area->permission());
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function role(): ?Role
    {
        return $this->roles->first();
    }

    public function scopeActiveAdmins(Builder $query): Builder
    {
        return $query->where('is_active', true)->role(Role::ADMIN);
    }

    /** Último login registrado pelo filament-logger (log "Access") */
    public function scopeWithLastLogin(Builder $query): Builder
    {
        return $query->addSelect([
            'last_login_at' => Activity::query()
                ->select('created_at')
                ->where('log_name', config('filament-logger.access.log_name', 'Access'))
                ->where('event', 'Login')
                ->whereColumn('causer_id', 'users.id')
                ->where('causer_type', static::class)
                ->latest()
                ->limit(1),
        ]);
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
        return $this->morphMany(Activity::class, 'causer')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Meu Workspace: tarefas, compromissos e notas pessoais.
     */
    public function workspaceTasks()
    {
        return $this->hasMany(WorkspaceTask::class);
    }

    public function workspaceEvents()
    {
        return $this->hasMany(WorkspaceEvent::class);
    }

    public function workspaceNotes()
    {
        return $this->hasMany(WorkspaceNote::class);
    }
}
