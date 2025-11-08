<?php

namespace App\Observers;

use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetObserver
{
    /**
     * Handle the Budget "updated" event.
     * Add custom price change description to the activity log
     */
    public function updated(Budget $budget): void
    {
        // Get original and new content
        $original = $budget->getOriginal('content');
        $new = $budget->content;

        // Check if content exists and is array
        if (! is_array($original) || ! is_array($new)) {
            return;
        }

        // Detect price changes
        $priceFields = ['price', 'tax', 'discount', 'total'];
        $priceChanges = [];

        foreach ($priceFields as $field) {
            $oldValue = $original[$field] ?? null;
            $newValue = $new[$field] ?? null;

            if ($oldValue != $newValue && ($oldValue !== null || $newValue !== null)) {
                $priceChanges[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // Only log if ONLY price fields changed (to avoid duplication with normal updates)
        // Check if other fields besides price fields changed
        $otherChanges = false;
        foreach (array_keys($original) as $key) {
            if (! in_array($key, $priceFields) && ($original[$key] ?? null) != ($new[$key] ?? null)) {
                $otherChanges = true;
                break;
            }
        }

        // If there are price changes AND no other significant changes, log specifically
        if (! empty($priceChanges) && ! $otherChanges) {
            $userName = Auth::user()?->name ?? 'System';
            $changes = [];

            foreach ($priceChanges as $field => $values) {
                $fieldLabel = match ($field) {
                    'price'    => 'Preço',
                    'tax'      => 'Taxa',
                    'discount' => 'Desconto',
                    'total'    => 'Total',
                    default    => ucfirst($field),
                };

                $oldFormatted = $values['old'] ? 'R$ '.number_format((float) $values['old'], 2, ',', '.') : 'N/A';
                $newFormatted = $values['new'] ? 'R$ '.number_format((float) $values['new'], 2, ',', '.') : 'N/A';

                $changes[] = "{$fieldLabel}: {$oldFormatted} → {$newFormatted}";
            }

            $description = 'price_updated: '.implode(', ', $changes);

            // Update the last activity log with custom description instead of creating new one
            $lastActivity = $budget->activities()->latest()->first();
            if ($lastActivity && $lastActivity->description === 'updated' &&
                $lastActivity->created_at->diffInSeconds(now()) < 2) {
                // Update the existing log with our custom description
                $lastActivity->description = $description;
                $properties = $lastActivity->properties->toArray();
                $properties['price_changes'] = $priceChanges;
                $lastActivity->properties = $properties;
                $lastActivity->save();
            }
        }
    }

    /**
     * Handle the Budget "creating" event.
     */
    public function creating(Budget $budget): void
    {
        // Ensure user_id is set
        if (! $budget->user_id && Auth::check()) {
            $budget->user_id = Auth::id();
        }
    }
}
