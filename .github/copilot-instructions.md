# Copilot Instructions for git-stats

A project that generates GitHub README stats cards as SVG images via an API. Available in TypeScript and PHP.

## Commands

### TypeScript (root directory)
```bash
npm run build        # Compile TypeScript
npm run dev          # Start Express dev server (requires GITHUB_TOKEN in .env)
npm test             # Run all tests
npm test -- --testPathPattern="stats.test"  # Run single test file
npm run test:watch   # Run tests in watch mode
```

### PHP (php/ directory)
```bash
cd php
composer install                     # Install dependencies
composer dev                         # Start PHP dev server on localhost:3000
composer test                        # Run all tests
./vendor/bin/phpunit --filter=StatsCard  # Run single test class
```

## API Endpoints (PHP)

```
/api/stats?username=USER              # Stats card
/api/stats?username=USER&theme=dark   # Stats card (dark mode)
/api/top-langs?username=USER          # Top languages card
/api/top-langs?username=USER&theme=dark&langs_count=6
```

**Common parameters:** `theme` (light/dark), `hide_title`, `hide_border`, `card_width`, `custom_title`

## Architecture

Both implementations share the same structure:

```
src/ (or php/src/)
├── api/ (Api/)       # Express/PHP server with /api/stats and /api/top-langs endpoints
├── fetchers/         # GitHub GraphQL API data fetching (stats, languages)
├── renderers/        # SVG card generation (card = base, stats-card, languages-card)
├── types/            # TypeScript interfaces / PHP classes (includes Theme for dark mode)
└── utils/            # Helpers (http, retryer, formatter, rank calculation)
```

**Data flow**: API endpoint → fetcher (GitHub GraphQL) → renderer (SVG output)

## Key Patterns

- Tests mirror the `src/` folder structure (TypeScript: `src/tests/`, PHP: `php/tests/`)
- GraphQL queries are embedded directly in fetcher files
- Renderers build SVG strings using template literals / heredocs
- Theme system in `Types/Theme.php` provides light/dark color presets
- Environment config via `dotenv` with `.env` file (requires `GITHUB_TOKEN`)
