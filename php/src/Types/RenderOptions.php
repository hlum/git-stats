<?php

declare(strict_types=1);

namespace App\Types;

class RenderOptions
{
    public function __construct(
        public readonly array $hide = [],
        public readonly bool $showIcons = false,
        public readonly bool $hideTitle = false,
        public readonly bool $hideBorder = false,
        public readonly bool $hideRank = false,
        public readonly int $cardWidth = 450,
        public readonly int $lineHeight = 25,
        public readonly ?string $theme = null,
        public readonly ?string $customTitle = null,
        public readonly int $borderRadius = 5,
        public readonly bool $disableAnimations = false,
        public readonly ?CardColors $colors = null
    ) {}
}
