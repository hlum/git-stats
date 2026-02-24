<?php

declare(strict_types=1);

namespace App\Fetchers;

use App\Types\FetchStatsOptions;
use App\Types\GitHubUser;
use App\Types\StatsData;
use App\Utils\HttpClient;
use App\Utils\RankCalculator;
use App\Utils\Retryer;
use Exception;

class StatsFetcher
{
    private const STATS_QUERY = <<<'GRAPHQL'
    query userInfo($login: String!) {
        user(login: $login) {
            name
            login
            commits: contributionsCollection {
                totalCommitContributions
            }
            reviews: contributionsCollection {
                totalPullRequestReviewContributions
            }
            repositoriesContributedTo(first: 1, contributionTypes: [COMMIT, ISSUE, PULL_REQUEST, REPOSITORY]) {
                totalCount
            }
            pullRequests(first: 1) {
                totalCount
            }
            mergedPullRequests: pullRequests(states: MERGED) {
                totalCount
            }
            openIssues: issues(states: OPEN) {
                totalCount
            }
            closedIssues: issues(states: CLOSED) {
                totalCount
            }
            followers {
                totalCount
            }
            repositories(first: 100, ownerAffiliations: OWNER, orderBy: {direction: DESC, field: STARGAZERS}) {
                totalCount
                nodes {
                    name
                    stargazers {
                        totalCount
                    }
                }
                pageInfo {
                    hasNextPage
                    endCursor
                }
            }
        }
    }
    GRAPHQL;

    private HttpClient $httpClient;

    public function __construct(?HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new HttpClient();
    }

    /**
     * @throws Exception
     */
    public function fetchStats(FetchStatsOptions $options): StatsData
    {
        $token = $_ENV['GITHUB_TOKEN'] ?? getenv('GITHUB_TOKEN');

        if (!$token) {
            throw new Exception('GITHUB_TOKEN is not set in environment variables.');
        }

        if (empty($options->username)) {
            throw new Exception('Username is required to fetch stats.');
        }

        $response = Retryer::retry(fn () => $this->fetcher($options->username, $token));

        if (isset($response['errors'])) {
            $error = $response['errors'][0];
            throw new Exception($error['message'] ?? 'Failed to fetch stats.');
        }

        $user = GitHubUser::fromArray($response['data']['user']);

        // Calculate total stars (excluding specified repos)
        $excludeSet = array_flip($options->excludeRepos);
        $totalStars = 0;
        foreach ($user->repositories['nodes'] as $repo) {
            if (!isset($excludeSet[$repo['name']])) {
                $totalStars += $repo['stargazers']['totalCount'];
            }
        }

        $totalCommits = $user->commits['totalCommitContributions'];
        $totalPRs = $user->pullRequests['totalCount'];
        $totalIssues = $user->openIssues['totalCount'] + $user->closedIssues['totalCount'];

        return new StatsData(
            name: $user->name ?? $user->login,
            totalCommits: $totalCommits,
            totalPRs: $totalPRs,
            totalPRsMerged: $user->mergedPullRequests['totalCount'],
            mergedPRsPercentage: $totalPRs > 0
                ? ($user->mergedPullRequests['totalCount'] / $totalPRs) * 100
                : 0.0,
            totalReviews: $user->reviews['totalPullRequestReviewContributions'],
            totalIssues: $totalIssues,
            totalStars: $totalStars,
            contributedTo: $user->repositoriesContributedTo['totalCount'],
            rank: RankCalculator::calculate(
                commits: $totalCommits,
                prs: $totalPRs,
                issues: $totalIssues,
                stars: $totalStars,
                followers: $user->followers['totalCount']
            )
        );
    }

    private function fetcher(string $username, string $token): array
    {
        return $this->httpClient->request(
            [
                'query' => self::STATS_QUERY,
                'variables' => ['login' => $username],
            ],
            [
                'Authorization' => "bearer {$token}",
            ]
        );
    }
}
