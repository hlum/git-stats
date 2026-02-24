<?php

declare(strict_types=1);

namespace App\Renderers;

use App\Types\CardColors;

class Card
{
    public static function create(
        int $width,
        int $height,
        CardColors $colors,
        string $content,
        string $title = '',
        bool $hideTitle = false,
        bool $hideBorder = false
    ): string {
        $borderStyle = $hideBorder
            ? ''
            : sprintf('stroke="%s" stroke-width="1"', $colors->borderColor);

        $titleElement = '';
        if (!$hideTitle && $title !== '') {
            $titleElement = sprintf(
                '<text x="25" y="35" class="header" fill="%s">%s</text>',
                $colors->titleColor,
                htmlspecialchars($title)
            );
        }

        return <<<SVG
<svg width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" 
     xmlns="http://www.w3.org/2000/svg" role="img">
  <title>{$title}</title>
  <rect 
    x="0.5" y="0.5" 
    width="{($width - 1)}" height="{($height - 1)}" 
    rx="4.5" 
    fill="{$colors->bgColor}" 
    {$borderStyle}
  />
  {$titleElement}
  {$content}
  <style>
    .header { font: 600 18px 'Segoe UI', Ubuntu, Sans-Serif; }
    .stat { font: 600 14px 'Segoe UI', Ubuntu, Sans-Serif; fill: {$colors->textColor}; }
    .bold { font-weight: 700; }
  </style>
</svg>
SVG;
    }
}
