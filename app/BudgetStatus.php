<?php

namespace App;

use Filament\Forms\Get;
use Filament\Forms\Set;

trait BudgetStatus
{
    private function updateBudgetStatus(Get $get, Set $set, ?string $state): string
    {
        $tax = $get('content.tax');
        $discount = $get('content.discount');
        $currentStatus = $get('status');
        if ($currentStatus === 'pending' && ($tax != 0 or $tax != null) && ($discount != 0 or $discount != null)) {
            return $set('status', 'on going');
        } else {
            return $set('status', $currentStatus);
        }
    }
}
