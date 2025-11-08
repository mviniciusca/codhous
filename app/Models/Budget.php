<?php

namespace App\Models;

use App\Models\Scopes\UserBudgetScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Budget extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserBudgetScope);

        // Automatically set user_id when creating
        static::creating(function ($budget) {
            if (! $budget->user_id && \Illuminate\Support\Facades\Auth::check()) {
                $budget->user_id = \Illuminate\Support\Facades\Auth::id();
            }
        });
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
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject');
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
     *
     * @return string
     */
    public static function generateUniqueCode(): string
    {
        $prefix = 'BD';
        $year = date('Y');
        $month = date('m');

        // Get the last budget created in this month
        $lastBudget = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBudget) {
            // Extract the sequence number from the last code
            $lastSequence = (int) substr($lastBudget->code, -5);
            $sequence = str_pad($lastSequence + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $sequence = '00001';
        }

        return sprintf('%s%s%s%s', $prefix, $year, $month, $sequence);
    }
}
