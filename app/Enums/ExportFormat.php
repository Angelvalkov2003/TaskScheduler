<?php

namespace App\Enums;

enum ExportFormat: string
{
    case JSON = 'json';
    case XLSX = 'xlsx';
    case SPSS = 'spss';
    case TRIPLE_S = 'tripleS';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
