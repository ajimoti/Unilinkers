<?php

namespace App\Enums;

enum SizeUnit: string
{
    case SQUARE_FEET = 'sqft';
    case SQUARE_METERS = 'sqm';

    public function title(): string
    {
        return match($this)
        {
            self::SQUARE_FEET => 'square feet',
            self::SQUARE_METERS => 'square meters',
        };
    }

    public static function values(): array
    {
        return collect(self::cases())->map(fn($sizeUnit) => $sizeUnit->value)->toArray();
    }
}
