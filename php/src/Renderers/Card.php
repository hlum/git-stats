<?php

declare(strict_types=1);

namespace App\Renderers;

use App\Types\CardColors;
use App\Types\RenderOptions;

class Card
{
    public static function create(
        int $width,
        int $height,
        CardColors $colors,
        string $content,
        string $title = '',
        ?RenderOptions $options = null
    ): string {
        $options = $options ?? new RenderOptions();
        
        $borderStyle = $options->hideBorder
            ? ''
            : sprintf('stroke="%s" stroke-width="1"', $colors->borderColor);

        $titleElement = '';
        if (!$options->hideTitle && $title !== '') {
            $titleElement = sprintf(
                '<text x="%d" y="%d" class="header" fill="%s">%s</text>',
                $options->paddingX,
                $options->titleOffsetY,
                $colors->titleColor,
                htmlspecialchars($title)
            );
        }

        $borderRadius = $options->borderRadius;
        $titleFontSize = $options->titleFontSize;
        $textFontSize = $options->textFontSize;
        
        // Pre-calculate dimensions (PHP heredoc can't evaluate expressions)
        $rectWidth = $width - 1;
        $rectHeight = $height - 1;

        return <<<SVG
<svg width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" 
     xmlns="http://www.w3.org/2000/svg" role="img">
  <title>{$title}</title>
  <rect 
    x="0.5" y="0.5" 
    width="{$rectWidth}" height="{$rectHeight}" 
    rx="{$borderRadius}" 
    fill="{$colors->bgColor}" 
    {$borderStyle}
  />
  {$titleElement}
  {$content}
  <style>
    .header { font: 600 {$titleFontSize}px 'Segoe UI', Ubuntu, Sans-Serif; }
    .stat { font: 600 {$textFontSize}px 'Segoe UI', Ubuntu, Sans-Serif; fill: {$colors->textColor}; }
    .bold { font-weight: 700; }
  </style>
</svg>
SVG;
    }
}
