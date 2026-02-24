<?php

declare(strict_types=1);

namespace App\Renderers;

use App\Types\CardColors;
use App\Types\RenderOptions;
use App\Types\Theme;

class LanguagesCard
{
    /**
     * @param array<string, array{size: int, color: string, percentage: float}> $languages
     */
    public static function render(
        array $languages,
        string $username,
        ?RenderOptions $options = null,
        int $langsCount = 5
    ): string {
        $options = $options ?? new RenderOptions();
        
        // Resolve colors: explicit colors > theme > default
        if ($options->colors !== null) {
            $colors = $options->colors;
        } elseif ($options->theme !== null) {
            $colors = Theme::getColors($options->theme);
        } else {
            $colors = new CardColors();
        }
        
        $title = $options->customTitle ?? "Most Used Languages";

        // Limit to top N languages
        $topLanguages = array_slice($languages, 0, $langsCount, true);

        // Recalculate percentages for visible languages
        $totalVisible = array_sum(array_column($topLanguages, 'size'));
        if ($totalVisible > 0) {
            foreach ($topLanguages as $name => $data) {
                $topLanguages[$name]['percentage'] = ($data['size'] / $totalVisible) * 100;
            }
        }

        // Calculate dimensions
        $barHeight = 8;
        $langItemHeight = 25;
        $yOffset = $options->hideTitle ? 30 : 55;
        $height = $yOffset + (count($topLanguages) * $langItemHeight) + 20;
        $barWidth = $options->cardWidth - 50;

        // Build progress bar
        $progressBar = self::createProgressBar($topLanguages, 25, $yOffset - 20, $barWidth, $barHeight);

        // Build language items
        $langItems = self::createLanguageItems($topLanguages, $yOffset, $colors);

        $content = $progressBar . $langItems;

        return Card::create(
            $options->cardWidth,
            $height,
            $colors,
            $content,
            $title,
            $options->hideTitle,
            $options->hideBorder
        );
    }

    /**
     * @param array<string, array{size: int, color: string, percentage: float}> $languages
     */
    private static function createProgressBar(
        array $languages,
        int $x,
        int $y,
        int $width,
        int $height
    ): string {
        $bars = '';
        $currentX = 0;

        foreach ($languages as $name => $data) {
            $barWidth = ($data['percentage'] / 100) * $width;
            $color = htmlspecialchars($data['color']);
            $safeName = htmlspecialchars($name);

            $bars .= <<<SVG
      <rect 
        x="{$currentX}" y="0" 
        width="{$barWidth}" height="{$height}" 
        fill="{$color}"
        data-testid="lang-{$safeName}"
      />
SVG;
            $currentX += $barWidth;
        }

        return <<<SVG
    <g transform="translate({$x}, {$y})">
      <rect x="0" y="0" width="{$width}" height="{$height}" rx="3" fill="transparent"/>
      <svg x="0" y="0" width="{$width}" height="{$height}">
        <mask id="lang-mask">
          <rect x="0" y="0" width="{$width}" height="{$height}" rx="3" fill="white"/>
        </mask>
        <g mask="url(#lang-mask)">
          {$bars}
        </g>
      </svg>
    </g>
SVG;
    }

    /**
     * @param array<string, array{size: int, color: string, percentage: float}> $languages
     */
    private static function createLanguageItems(
        array $languages,
        int $startY,
        CardColors $colors
    ): string {
        $items = '';
        $index = 0;
        $colWidth = 200;

        foreach ($languages as $name => $data) {
            $col = $index % 2;
            $row = intdiv($index, 2);
            $x = 25 + ($col * $colWidth);
            $y = $startY + ($row * 25);

            $color = htmlspecialchars($data['color']);
            $safeName = htmlspecialchars($name);
            $percentage = number_format($data['percentage'], 2);

            $items .= <<<SVG
    <g transform="translate({$x}, {$y})">
      <circle cx="5" cy="6" r="5" fill="{$color}"/>
      <text class="stat" x="15" y="10" fill="{$colors->textColor}">
        {$safeName} <tspan fill="{$colors->textColor}" opacity="0.7">{$percentage}%</tspan>
      </text>
    </g>
SVG;
            $index++;
        }

        return $items;
    }
}
