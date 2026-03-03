<?php

declare(strict_types=1);

namespace Tests\Renderers;

use App\Renderers\StatsCard;
use App\Types\Rank;
use App\Types\RenderOptions;
use App\Types\StatsData;
use PHPUnit\Framework\TestCase;

class StatsCardTest extends TestCase
{
    private StatsData $mockStats;

    protected function setUp(): void
    {
        $this->mockStats = new StatsData(
            name: 'Test User',
            totalCommits: 1000,
            totalPRs: 50,
            totalPRsMerged: 40,
            mergedPRsPercentage: 80.0,
            totalReviews: 30,
            totalIssues: 20,
            totalStars: 500,
            contributedTo: 10,
            rank: new Rank('A', 25.0)
        );
    }

    public function testRendersStatsWithSvg(): void
    {
        $svg = StatsCard::render($this->mockStats);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Test User', $svg);
        $this->assertStringContainsString('Total Stars', $svg);
    }

    public function testHidesStatsWhenSpecified(): void
    {
        $options = new RenderOptions(hide: ['stars']);
        $svg = StatsCard::render($this->mockStats, $options);

        $this->assertStringNotContainsString('Total Stars', $svg);
    }

    public function testHidesTitleWhenHideTitleIsTrue(): void
    {
        $options = new RenderOptions(hideTitle: true);
        $svg = StatsCard::render($this->mockStats, $options);

        // The header text element should not be present (though <title> element still exists)
        $this->assertStringNotContainsString('class="header"', $svg);
    }

    public function testUsesCustomTitle(): void
    {
        $options = new RenderOptions(customTitle: 'My Custom Stats');
        $svg = StatsCard::render($this->mockStats, $options);

        $this->assertStringContainsString('My Custom Stats', $svg);
    }
}
