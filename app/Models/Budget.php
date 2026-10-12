<?php

namespace App\Models;

use App\Models\Scopes\UserBudgetScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Budget extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status_changed_at' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserBudgetScope);

        // Automatically set user_id and code when creating.
        // Passar 'user_id' => null explicitamente (pedidos do site) evita atribuir ao usuário logado.
        static::creating(function ($budget) {
            if (! array_key_exists('user_id', $budget->getAttributes()) && Auth::check()) {
                $budget->user_id = Auth::id();
            }

            if (! $budget->code) {
                $budget->code = self::generateUniqueCode();
            }

            $budget->status_changed_at ??= now();
        });

        static::updating(function ($budget) {
            if ($budget->status === 'pending') {
                $oldContent = $budget->getOriginal('content');
                $newContent = $budget->content;

                // Ensure we have arrays to compare
                $oldContent = is_array($oldContent) ? $oldContent : json_decode($oldContent ?? '[]', true);
                $newContent = is_array($newContent) ? $newContent : json_decode($newContent ?? '[]', true);

                $pricingFields = ['shipping', 'tax', 'discount'];
                $hasPricingChanges = false;

                foreach ($pricingFields as $field) {
                    $oldValue = $oldContent[$field] ?? null;
                    $newValue = $newContent[$field] ?? null;

                    // Compare values (ignoring type difference like "0" vs 0)
                    if ($oldValue != $newValue) {
                        $hasPricingChanges = true;
                        break;
                    }
                }

                if ($hasPricingChanges) {
                    $budget->status = 'on going';
                }
            }
        });

        // Registrado depois da regra acima, para também cobrir a troca automática de etapa
        static::updating(function ($budget) {
            if ($budget->isDirty('status')) {
                $budget->status_changed_at = now();
            }
        });
    }

    /** Dias sem mudar de etapa para um orçamento em negociação ser considerado parado */
    public const STALE_AFTER_DAYS = 3;

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'on going'], true);
    }

    /**
     * Dias inteiros na etapa atual.
     */
    public function daysInStage(): int
    {
        return (int) ($this->status_changed_at ?? $this->updated_at ?? now())->diffInDays(now());
    }

    public function isStale(): bool
    {
        return $this->status === 'on going' && $this->daysInStage() >= self::STALE_AFTER_DAYS;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Log all attributes for complete history
            ->logOnlyDirty() // But only when something actually changed
            ->dontSubmitEmptyLogs()
            ->useLogName('budget');
    }

    /**
     * Get the user who created this budget.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'budget_product')
            ->withPivot(['quantity', 'price', 'subtotal', 'product_option_id', 'location_id'])
            ->withTimestamps();
    }

    public function pdfs()
    {
        return $this->hasMany(BudgetPdf::class);
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function documents()
    {
        return $this->hasMany(BudgetDocument::class);
    }

    public function latestPdf()
    {
        return $this->pdfs()->latest()->first();
    }

    /**
     * Generate a unique budget code
     */
    public static function generateUniqueCode(): string
    {
        $prefix = 'OR';
        $year = date('Y');

        // Get the last budget created in this year with the same prefix
        $lastBudget = self::withoutGlobalScopes()
            ->where('code', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBudget) {
            // Extract the sequence number from the last code (OR-YYYY-XXXXX)
            $parts = explode('-', $lastBudget->code);
            $lastSequence = (int) end($parts);
            $sequence = str_pad($lastSequence + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $sequence = '00001';
        }

        return sprintf('%s-%s-%s', $prefix, $year, $sequence);
    }
}
