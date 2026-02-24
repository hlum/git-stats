<?php

declare(strict_types=1);

namespace App\Fetchers;

use App\Utils\HttpClient;
use App\Utils\Retryer;
use Exception;

class LanguagesFetcher
{
    private const LANGUAGES_QUERY = <<<'GRAPHQL'
    query userLanguages($login: String!, $first: Int!) {
        user(login: $login) {
            repositories(first: $first, ownerAffiliations: OWNER, isFork: false, orderBy: {direction: DESC, field: STARGAZERS}) {
                nodes {
                    name
                    languages(first: 10, orderBy: {direction: DESC, field: SIZE}) {
                        edges {
                            size
                            node {
                                name
                                color
                            }
                        }
                    }
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
     * @return array<string, array{size: int, color: string, percentage: float}>
     * @throws Exception
     */
    public function fetchLanguages(string $username, int $repoCount = 100): array
    {
        $token = $_ENV['GITHUB_TOKEN'] ?? getenv('GITHUB_TOKEN');

        if (!$token) {
            throw new Exception('GITHUB_TOKEN is not set in environment variables.');
        }

        if (empty($username)) {
            throw new Exception('Username is required to fetch languages.');
        }

        $response = Retryer::retry(fn () => $this->fetcher($username, $repoCount, $token));

        if (isset($response['errors'])) {
            $error = $response['errors'][0];
            throw new Exception($error['message'] ?? 'Failed to fetch languages.');
        }

        return $this->aggregateLanguages($response['data']['user']['repositories']['nodes']);
    }

    private function fetcher(string $username, int $repoCount, string $token): array
    {
        return $this->httpClient->request(
            [
                'query' => self::LANGUAGES_QUERY,
                'variables' => [
                    'login' => $username,
                    'first' => min($repoCount, 100),
                ],
            ],
            [
                'Authorization' => "bearer {$token}",
            ]
        );
    }

    /**
     * @return array<string, array{size: int, color: string, percentage: float}>
     */
    private function aggregateLanguages(array $repositories): array
    {
        $languages = [];

        foreach ($repositories as $repo) {
            if (!isset($repo['languages']['edges'])) {
                continue;
            }

            foreach ($repo['languages']['edges'] as $edge) {
                $name = $edge['node']['name'];
                $size = $edge['size'];
                $color = $edge['node']['color'] ?? '#858585';

                if (!isset($languages[$name])) {
                    $languages[$name] = [
                        'size' => 0,
                        'color' => $color,
                    ];
                }
                $languages[$name]['size'] += $size;
            }
        }

        // Sort by size descending
        uasort($languages, fn ($a, $b) => $b['size'] <=> $a['size']);

        // Calculate percentages
        $totalSize = array_sum(array_column($languages, 'size'));
        if ($totalSize > 0) {
            foreach ($languages as $name => $data) {
                $languages[$name]['percentage'] = ($data['size'] / $totalSize) * 100;
            }
        }

        return $languages;
    }
}
