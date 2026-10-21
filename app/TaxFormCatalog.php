<?php

namespace App;

class TaxFormCatalog
{
    private const LABELS = [
        'MAP' => 'MAP',
        'QAP' => '1604-E',
        'SAWT' => 'SAWT',
        'Annual' => '1604-CF',
    ];

    public static function labels(): array
    {
        return self::LABELS;
    }

    public static function label(?string $taxForm): string
    {
        return self::LABELS[$taxForm] ?? ($taxForm ?? 'Unknown');
    }
}
