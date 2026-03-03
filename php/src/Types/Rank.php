<?php

declare(strict_types=1);

namespace App\Types;

class Rank
{
    public function __construct(
        public readonly string $level,
        public readonly float $percentile
    ) {}
}
