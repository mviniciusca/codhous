<?php

namespace App\Services;

use App\Enums\FakeDataEnum;

class FakeBudgetDataService
{
    /**
     * Generate complete fake budget data from Enum
     *
     * @return array
     */
    public function generateCompleteBudgetData(): array
    {
        return FakeDataEnum::asArray();
    }
}
