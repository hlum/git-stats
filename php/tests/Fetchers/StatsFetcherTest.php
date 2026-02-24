<?php

declare(strict_types=1);

namespace Tests\Fetchers;

use App\Fetchers\StatsFetcher;
use App\Types\FetchStatsOptions;
use PHPUnit\Framework\TestCase;

class StatsFetcherTest extends TestCase
{
    protected function setUp(): void
    {
        // Clear GITHUB_TOKEN for testing
        putenv('GITHUB_TOKEN');
        unset($_ENV['GITHUB_TOKEN']);
    }

    public function testThrowsErrorIfUsernameIsMissing(): void
    {
        $_ENV['GITHUB_TOKEN'] = 'test-token';

        $fetcher = new StatsFetcher();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Username is required');

        $fetcher->fetchStats(new FetchStatsOptions(''));
    }

    public function testThrowsErrorIfGitHubTokenIsMissing(): void
    {
        $fetcher = new StatsFetcher();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('GITHUB_TOKEN is not set');

        $fetcher->fetchStats(new FetchStatsOptions('testuser'));
    }
}
