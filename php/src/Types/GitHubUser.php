<?php

declare(strict_types=1);

namespace App\Types;

class GitHubUser
{
    public function __construct(
        public readonly ?string $name,
        public readonly string $login,
        public readonly array $commits,
        public readonly array $reviews,
        public readonly array $repositoriesContributedTo,
        public readonly array $pullRequests,
        public readonly array $mergedPullRequests,
        public readonly array $openIssues,
        public readonly array $closedIssues,
        public readonly array $followers,
        public readonly array $repositories
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            login: $data['login'],
            commits: $data['commits'],
            reviews: $data['reviews'],
            repositoriesContributedTo: $data['repositoriesContributedTo'],
            pullRequests: $data['pullRequests'],
            mergedPullRequests: $data['mergedPullRequests'] ?? ['totalCount' => 0],
            openIssues: $data['openIssues'],
            closedIssues: $data['closedIssues'],
            followers: $data['followers'],
            repositories: $data['repositories']
        );
    }
}
