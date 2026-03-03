<?php

declare(strict_types=1);

namespace App\Types;

class Theme
{
    private const THEMES = [
        'light' => [
            'titleColor' => '#2f80ed',
            'textColor' => '#434d58',
            'iconColor' => '#4c71f2',
            'bgColor' => '#fffefe',
            'borderColor' => '#e4e2e2',
            'ringColor' => '#2f80ed',
        ],
        'dark' => [
            'titleColor' => '#58a6ff',
            'textColor' => '#c9d1d9',
            'iconColor' => '#58a6ff',
            'bgColor' => '#0d1117',
            'borderColor' => '#30363d',
            'ringColor' => '#58a6ff',
        ],
    ];

    public static function getColors(string $theme): CardColors
    {
        $colors = self::THEMES[$theme] ?? self::THEMES['light'];

        return new CardColors(
            titleColor: $colors['titleColor'],
            textColor: $colors['textColor'],
            iconColor: $colors['iconColor'],
            bgColor: $colors['bgColor'],
            borderColor: $colors['borderColor'],
            ringColor: $colors['ringColor']
        );
    }

    public static function isValidTheme(string $theme): bool
    {
        return isset(self::THEMES[$theme]);
    }

    public static function getAvailableThemes(): array
    {
        return array_keys(self::THEMES);
    }
}
