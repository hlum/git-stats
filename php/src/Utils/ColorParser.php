<?php

declare(strict_types=1);

namespace App\Utils;

use App\Types\CardColors;
use App\Types\Theme;

class ColorParser
{
    // Named colors for easy customization
    private const NAMED_COLORS = [
        // Basic colors
        'white' => '#ffffff',
        'black' => '#000000',
        'red' => '#ff0000',
        'green' => '#00ff00',
        'blue' => '#0000ff',
        'yellow' => '#ffff00',
        'cyan' => '#00ffff',
        'magenta' => '#ff00ff',
        'orange' => '#ff8c00',
        'purple' => '#800080',
        'pink' => '#ffc0cb',
        'gray' => '#808080',
        'grey' => '#808080',
        
        // GitHub colors
        'github-dark' => '#0d1117',
        'github-light' => '#ffffff',
        'github-blue' => '#58a6ff',
        'github-green' => '#3fb950',
        'github-red' => '#f85149',
        'github-yellow' => '#d29922',
        'github-purple' => '#a371f7',
        'github-border-dark' => '#30363d',
        'github-border-light' => '#d0d7de',
        'github-text-dark' => '#c9d1d9',
        'github-text-light' => '#24292f',
        
        // Popular theme colors
        'dracula-bg' => '#282a36',
        'dracula-text' => '#f8f8f2',
        'dracula-pink' => '#ff79c6',
        'dracula-purple' => '#bd93f9',
        'dracula-cyan' => '#8be9fd',
        'dracula-green' => '#50fa7b',
        
        'monokai-bg' => '#272822',
        'monokai-text' => '#f8f8f2',
        'monokai-pink' => '#f92672',
        'monokai-green' => '#a6e22e',
        'monokai-yellow' => '#e6db74',
        'monokai-blue' => '#66d9ef',
        
        'nord-bg' => '#2e3440',
        'nord-text' => '#eceff4',
        'nord-blue' => '#88c0d0',
        'nord-green' => '#a3be8c',
        'nord-red' => '#bf616a',
        
        'solarized-dark-bg' => '#002b36',
        'solarized-light-bg' => '#fdf6e3',
        'solarized-blue' => '#268bd2',
        'solarized-cyan' => '#2aa198',
        'solarized-green' => '#859900',
        
        // Transparent
        'transparent' => 'transparent',
        'none' => 'transparent',
    ];

    /**
     * Build CardColors from request parameters.
     * Priority: custom colors > theme > defaults
     */
    public static function fromParams(array $params): CardColors
    {
        // Start with theme or default colors
        $theme = $params['theme'] ?? null;
        if ($theme !== null && Theme::isValidTheme($theme)) {
            $colors = Theme::getColors($theme);
        } else {
            $colors = new CardColors();
        }

        // Override with custom colors if provided
        return new CardColors(
            titleColor: self::parseColor($params['title_color'] ?? null) ?? $colors->titleColor,
            textColor: self::parseColor($params['text_color'] ?? null) ?? $colors->textColor,
            iconColor: self::parseColor($params['icon_color'] ?? null) ?? $colors->iconColor,
            bgColor: self::parseColor($params['bg_color'] ?? null) ?? $colors->bgColor,
            borderColor: self::parseColor($params['border_color'] ?? null) ?? $colors->borderColor,
            ringColor: self::parseColor($params['ring_color'] ?? null) ?? $colors->ringColor
        );
    }

    /**
     * Get list of available named colors
     */
    public static function getNamedColors(): array
    {
        return self::NAMED_COLORS;
    }

    /**
     * Parse a color value - supports named colors, hex with or without #
     */
    private static function parseColor(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remove any whitespace and convert to lowercase for named color lookup
        $value = trim($value);
        $lowerValue = strtolower($value);

        // Check if it's a named color
        if (isset(self::NAMED_COLORS[$lowerValue])) {
            return self::NAMED_COLORS[$lowerValue];
        }

        // Add # if it's a hex color without it
        if (preg_match('/^[0-9a-fA-F]{3,8}$/', $value)) {
            return '#' . $value;
        }

        // Already has # or is a CSS color
        if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
            return $value;
        }

        // Return as-is for CSS named colors (red, blue, etc.) or rgb/rgba
        return $value;
    }
}
