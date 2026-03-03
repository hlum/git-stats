<?php

declare(strict_types=1);

namespace App\Types;

class StatsData
{
    public function __construct(
        public readonly string $name,
        public readonly int $totalPRs,
        public readonly int $totalPRsMerged,
        public readonly float $mergedPRsPercentage,
        public readonly int $totalReviews,
        public readonly int $totalCommits,
        public readonly int $totalIssues,
        public readonly int $totalStars,
        public readonly int $contributedTo,
        public readonly Rank $rank
    ) {}
}
