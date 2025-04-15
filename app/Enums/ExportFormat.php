<?php

namespace App\Enums;

enum ExportFormat: string
{
    case XLSX = 'csv';
    case SPSS = 'spss16';
    case TRIPLE_S = 'fwu';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function fileExtension(): string
    {
        return match ($this) {
            self::SPSS => 'zip',
            self::XLSX => 'csv',
            self::TRIPLE_S => 'fwu',
            default => 'txt',
        };
    }
}
