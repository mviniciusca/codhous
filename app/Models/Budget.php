<?php

namespace App\Models;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
