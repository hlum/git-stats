<?php

declare(strict_types=1);

namespace App\Utils;

class Formatter
{
    public static function kFormatter(int $num, int $precision = 1): string
    {
        if ($num >= 1000000) {
            return number_format($num / 1000000, $precision) . 'm';
        }
        if ($num >= 1000) {
            return number_format($num / 1000, $precision) . 'k';
        }
        return (string) $num;
    }

    public static function clampValue(float $value, float $min, float $max): float
    {
        return min(max($value, $min), $max);
    }
}
