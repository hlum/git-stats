<?php

declare(strict_types=1);

namespace App\Utils;

use App\Types\Rank;

class RankCalculator
{
    private const COMMITS_WEIGHT = 2;
    private const PRS_WEIGHT = 3;
    private const ISSUES_WEIGHT = 1;
    private const STARS_WEIGHT = 4;
    private const FOLLOWERS_WEIGHT = 1;

    public static function calculate(
        int $commits,
        int $prs,
        int $issues,
        int $stars,
        int $followers
    ): Rank {
        $totalScore =
            $commits * self::COMMITS_WEIGHT +
            $prs * self::PRS_WEIGHT +
            $issues * self::ISSUES_WEIGHT +
            $stars * self::STARS_WEIGHT +
            $followers * self::FOLLOWERS_WEIGHT;

        $normalizedScore = log10($totalScore + 1);

        $level = 'C';
        $percentile = 100.0;

        if ($normalizedScore > 5) {
            $level = 'S+';
            $percentile = 1.0;
        } elseif ($normalizedScore > 4) {
            $level = 'S';
            $percentile = 10.0;
        } elseif ($normalizedScore > 3.5) {
            $level = 'A+';
            $percentile = 25.0;
        } elseif ($normalizedScore > 3) {
            $level = 'A';
            $percentile = 40.0;
        } elseif ($normalizedScore > 2.5) {
            $level = 'B+';
            $percentile = 60.0;
        } elseif ($normalizedScore > 2) {
            $level = 'B';
            $percentile = 80.0;
        }

        return new Rank($level, $percentile);
    }
}
