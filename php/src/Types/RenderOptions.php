<?php

declare(strict_types=1);

namespace App\Types;

class RenderOptions
{
    public function __construct(
        // Visibility
        public readonly array $hide = [],
        public readonly bool $showIcons = false,
        public readonly bool $hideTitle = false,
        public readonly bool $hideBorder = false,
        public readonly bool $hideRank = false,
        public readonly bool $disableAnimations = false,
        
        // Dimensions
        public readonly int $cardWidth = 450,
        public readonly ?int $cardHeight = null,  // null = auto-calculate
        public readonly int $borderRadius = 5,
        public readonly int $lineHeight = 25,
        
        // Offsets/Padding
        public readonly int $paddingX = 25,
        public readonly int $paddingY = 20,
        public readonly int $titleOffsetY = 35,
        
        // Font sizes
        public readonly int $titleFontSize = 18,
        public readonly int $textFontSize = 14,
        
        // Content
        public readonly ?string $theme = null,
        public readonly ?string $customTitle = null,
        public readonly ?CardColors $colors = null
    ) {}
}

