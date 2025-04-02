<?php

namespace App\Enums;

enum ExportFormat: string
{
    case JSON = 'json';
    case XLSX = 'csv';
    case SPSS = 'spss16';
    case TRIPLE_S = 'tripleS';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
