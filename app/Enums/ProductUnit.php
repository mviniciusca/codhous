<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductUnit: string implements HasLabel
{
    case M3 = 'm³';
    case KG = 'kg';
    case M2 = 'm²';
    case UNIT = 'unit';
    case DAY = 'day';
    case HOUR = 'hour';
    case WEEK = 'week';
    case MONTH = 'month';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::M3 => __('M³'),
            self::KG => __('Kg'),
            self::M2 => __('M²'),
            self::UNIT => __('Unit'),
            self::DAY => __('Day'),
            self::HOUR => __('Hour'),
            self::WEEK => __('Week'),
            self::MONTH => __('Month'),
        };
    }
}
