<?php

declare(strict_types=1);

namespace App\Types;

class FetchStatsOptions
{
    public function __construct(
        public readonly string $username,
        public readonly bool $includeAllCommits = false,
        public readonly array $excludeRepos = [],
        public readonly bool $includeMergedPRs = false
    ) {}
}
