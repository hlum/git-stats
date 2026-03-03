<?php

declare(strict_types=1);

namespace App\Renderers;

use App\Types\CardColors;
use App\Types\RenderOptions;

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
        $colors = $options->colors ?? new CardColors();
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

        // Calculate dimensions using options
        $paddingX = $options->paddingX;
        $paddingY = $options->paddingY;
        $lineHeight = $options->lineHeight;
        $barHeight = 8;
        
        // Calculate rows needed (2 columns layout)
        $rowCount = (int) ceil(count($topLanguages) / 2);
        $yOffset = $options->hideTitle ? ($paddingY + 10) : ($options->titleOffsetY + 25);
        $itemsStartY = $yOffset + $barHeight + 15;
        $height = $options->cardHeight ?? ($itemsStartY + ($rowCount * $lineHeight) + $paddingY);
        $barWidth = $options->cardWidth - ($paddingX * 2);

        // Build progress bar (positioned with padding from title)
        $barY = $yOffset;
        $progressBar = self::createProgressBar($topLanguages, $paddingX, $barY, $barWidth, $barHeight);

        // Build language items (with gap after progress bar)
        $itemsStartY = $barY + $barHeight + 15;
        $langItems = self::createLanguageItems($topLanguages, $itemsStartY, $colors, $paddingX, $lineHeight);

        $content = $progressBar . $langItems;

        return Card::create(
            $options->cardWidth,
            $height,
            $colors,
            $content,
            $title,
            $options
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
        CardColors $colors,
        int $paddingX,
        int $lineHeight
    ): string {
        $items = '';
        $index = 0;
        $colWidth = 200;

        foreach ($languages as $name => $data) {
            $col = $index % 2;
            $row = intdiv($index, 2);
            $x = $paddingX + ($col * $colWidth);
            $y = $startY + ($row * $lineHeight);

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
