<?php

declare(strict_types=1);

namespace App\Api;

use App\Fetchers\StatsFetcher;
use App\Renderers\StatsCard;
use App\Types\FetchStatsOptions;
use App\Types\RenderOptions;
use Exception;

class StatsController
{
    private StatsFetcher $fetcher;

    public function __construct(?StatsFetcher $fetcher = null)
    {
        $this->fetcher = $fetcher ?? new StatsFetcher();
    }

    public function handle(array $params): string
    {
        try {
            $username = $params['username'] ?? null;

            if (empty($username) || !is_string($username)) {
                throw new Exception('Username is required and must be a string');
            }

            // Fetch stats
            $stats = $this->fetcher->fetchStats(new FetchStatsOptions($username));

            // Parse hide options
            $hide = [];
            if (!empty($params['hide'])) {
                $hide = array_map('trim', explode(',', $params['hide']));
            }

            // Build render options
            $renderOptions = new RenderOptions(
                hide: $hide,
                hideTitle: ($params['hide_title'] ?? '') === 'true',
                hideBorder: ($params['hide_border'] ?? '') === 'true',
                hideRank: ($params['hide_rank'] ?? '') === 'true',
                cardWidth: isset($params['card_width']) ? (int) $params['card_width'] : 450,
                customTitle: $params['custom_title'] ?? null
            );

            return StatsCard::render($stats, $renderOptions);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    private function renderError(string $message): string
    {
        $escapedMessage = htmlspecialchars($message);

        return <<<SVG
<svg width="450" height="120" xmlns="http://www.w3.org/2000/svg">
  <rect width="450" height="120" fill="#fff" stroke="#e4e2e2"/>
  <text x="225" y="60" text-anchor="middle" fill="#d73a4a" 
        style="font: 600 16px 'Segoe UI', Ubuntu, Sans-Serif;">
    Error: {$escapedMessage}
  </text>
</svg>
SVG;
    }
}
