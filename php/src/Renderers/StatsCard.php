<?php

declare(strict_types=1);

namespace App\Renderers;

use App\Types\CardColors;
use App\Types\RenderOptions;
use App\Types\StatsData;
use App\Types\Theme;
use App\Utils\Formatter;

class StatsCard
{
    public static function render(StatsData $stats, ?RenderOptions $options = null): string
    {
        $options = $options ?? new RenderOptions();
        
        // Resolve colors: explicit colors > theme > default
        if ($options->colors !== null) {
            $colors = $options->colors;
        } elseif ($options->theme !== null) {
            $colors = Theme::getColors($options->theme);
        } else {
            $colors = new CardColors();
        }
        
        $title = $options->customTitle ?? "{$stats->name}'s GitHub Stats";

        // Define available stats
        $allStats = [
            'stars' => [
                'label' => 'Total Stars',
                'value' => Formatter::kFormatter($stats->totalStars),
                'id' => 'stars',
            ],
            'commits' => [
                'label' => 'Total Commits',
                'value' => Formatter::kFormatter($stats->totalCommits),
                'id' => 'commits',
            ],
            'prs' => [
                'label' => 'Total PRs',
                'value' => Formatter::kFormatter($stats->totalPRs),
                'id' => 'prs',
            ],
            'issues' => [
                'label' => 'Total Issues',
                'value' => Formatter::kFormatter($stats->totalIssues),
                'id' => 'issues',
            ],
            'contribs' => [
                'label' => 'Total Contributions',
                'value' => Formatter::kFormatter($stats->contributedTo),
                'id' => 'contribs',
            ],
        ];

        // Filter stats based on hide option
        $visibleStats = [];
        foreach ($allStats as $key => $stat) {
            if (!in_array($key, $options->hide, true)) {
                $visibleStats[] = $stat;
            }
        }

        // Calculate card dimensions
        $statHeight = count($allStats) * 25;
        $yOffset = $options->hideTitle ? 20 : 50;
        $height = $yOffset + $statHeight + 20;

        // Render stat items
        $statsContent = '';
        foreach ($visibleStats as $index => $stat) {
            $statsContent .= self::createStatItem($stat, $index, $yOffset);
        }

        // Render rank circle
        $rankCircle = '';
        if (!$options->hideRank) {
            $rankCircle = self::createRankCircle(
                $stats->rank->level,
                $stats->rank->percentile,
                $options->cardWidth - 80,
                (int) ($height / 2),
                $colors->ringColor
            );
        }

        $content = $statsContent . $rankCircle;

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

    private static function createStatItem(array $item, int $index, int $yOffset): string
    {
        $y = $yOffset + $index * 25;

        return <<<SVG
    <g transform="translate(0, {$y})">
      <text class="stat" x="25" y="12.5">{$item['label']}:</text>
      <text class="stat bold" x="220" y="12.5" data-testid="{$item['id']}">
        {$item['value']}
      </text>
    </g>
SVG;
    }

    private static function createRankCircle(
        string $level,
        float $percentile,
        int $x,
        int $y,
        string $ringColor
    ): string {
        $circleRadius = 40;
        $progress = 100 - $percentile;
        $circumference = 2 * M_PI * $circleRadius;
        $offset = $circumference * (1 - $progress / 100);

        return <<<SVG
    <g transform="translate({$x}, {$y})">
      <circle class="rank-circle-rim" cx="0" cy="0" r="40" 
              stroke="{$ringColor}" stroke-width="6" fill="none" opacity="0.2"/>
      <circle class="rank-circle" cx="0" cy="0" r="40" 
              stroke="{$ringColor}" stroke-width="6" fill="none" 
              stroke-dasharray="{$circumference}" 
              stroke-dashoffset="{$offset}"
              transform="rotate(-90)" 
              transform-origin="0 0"/>
      <text x="0" y="5" text-anchor="middle" class="stat bold" 
            style="font-size: 24px;">
        {$level}
      </text>
    </g>
SVG;
    }
}
