<?php

declare(strict_types=1);

namespace App\Types;

class CardColors
{
    public function __construct(
        public string $titleColor = '#2f80ed',
        public string $textColor = '#434d58',
        public string $iconColor = '#4c71f2',
        public string $bgColor = '#fffefe',
        public string $borderColor = '#e4e2e2',
        public string $ringColor = '#2f80ed'
    ) {}
}
