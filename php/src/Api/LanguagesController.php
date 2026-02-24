<?php

declare(strict_types=1);

namespace App\Api;

use App\Fetchers\LanguagesFetcher;
use App\Renderers\LanguagesCard;
use App\Types\RenderOptions;
use Exception;

class LanguagesController
{
    private LanguagesFetcher $fetcher;

    public function __construct(?LanguagesFetcher $fetcher = null)
    {
        $this->fetcher = $fetcher ?? new LanguagesFetcher();
    }

    public function handle(array $params): string
    {
        try {
            $username = $params['username'] ?? null;

            if (empty($username) || !is_string($username)) {
                throw new Exception('Username is required and must be a string');
            }

            // Fetch languages
            $languages = $this->fetcher->fetchLanguages($username);

            // Parse options
            $langsCount = isset($params['langs_count']) ? (int) $params['langs_count'] : 5;
            $langsCount = max(1, min($langsCount, 10)); // Clamp between 1-10

            // Build render options
            $renderOptions = new RenderOptions(
                hideTitle: ($params['hide_title'] ?? '') === 'true',
                hideBorder: ($params['hide_border'] ?? '') === 'true',
                cardWidth: isset($params['card_width']) ? (int) $params['card_width'] : 450,
                theme: $params['theme'] ?? null,
                customTitle: $params['custom_title'] ?? null
            );

            return LanguagesCard::render($languages, $username, $renderOptions, $langsCount);
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
