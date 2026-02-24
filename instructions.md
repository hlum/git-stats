Project: Recreate GitHub README Stats Card in TypeScript
===============================================================

## Overview
This guide provides step-by-step instructions to recreate the Stats Card feature from github-readme-stats in TypeScript. You'll build a standalone module that fetches GitHub user statistics and renders them as an SVG card.

## What You'll Build
- **Fetcher**: Module to fetch GitHub user stats via GraphQL API
- **Renderer**: SVG generator for the stats card
- **API Endpoint**: Express/Vercel serverless function
- **Types**: Full TypeScript type definitions
- **Tests**: Unit tests for core functionality

## Architecture Overview

```
stats-card-ts/
├── src/
│   ├── types/           # TypeScript interfaces
│   ├── fetchers/        # GitHub API data fetcher
│   ├── renderers/       # SVG card renderer
│   ├── utils/           # HTTP, cache, formatting helpers
│   └── api/             # Express endpoint
├── tests/               # Jest tests
├── tsconfig.json        # TypeScript config
└── package.json         # Dependencies
```

---

## Step-by-Step Implementation Guide

### Step 1: Project Setup & Configuration

**1.1 Create Project Directory**
```bash
mkdir stats-card-ts
cd stats-card-ts
npm init -y
```

**1.2 Install Dependencies**
```bash
# Core dependencies
npm install axios dotenv

# TypeScript and build tools
npm install -D typescript @types/node ts-node

# Testing
npm install -D jest ts-jest @types/jest

# Express (for API endpoint)
npm install express
npm install -D @types/express
```

**1.3 Create tsconfig.json**
```json
{
  "compilerOptions": {
    "target": "ES2020",
    "module": "commonjs",
    "lib": ["ES2020"],
    "outDir": "./dist",
    "rootDir": "./src",
    "strict": true,
    "esModuleInterop": true,
    "skipLibCheck": true,
    "forceConsistentCasingInFileNames": true,
    "resolveJsonModule": true,
    "declaration": true,
    "declarationMap": true,
    "sourceMap": true
  },
  "include": ["src/**/*"],
  "exclude": ["node_modules", "dist", "tests"]
}
```

**1.4 Update package.json scripts**
```json
{
  "scripts": {
    "build": "tsc",
    "dev": "ts-node src/api/index.ts",
    "test": "jest",
    "test:watch": "jest --watch"
  }
}
```

**1.5 Create Jest configuration (jest.config.js)**
```javascript
module.exports = {
  preset: 'ts-jest',
  testEnvironment: 'node',
  roots: ['<rootDir>/tests'],
  testMatch: ['**/*.test.ts'],
  collectCoverageFrom: ['src/**/*.ts']
};
```

---

### Step 2: Define TypeScript Types

**2.1 Create src/types/github.ts** (GitHub API Response Types)
```typescript
export interface GitHubUser {
  name: string;
  login: string;
  commits: {
    totalCommitContributions: number;
  };
  reviews: {
    totalPullRequestReviewContributions: number;
  };
  repositoriesContributedTo: {
    totalCount: number;
  };
  pullRequests: {
    totalCount: number;
  };
  mergedPullRequests?: {
    totalCount: number;
  };
  openIssues: {
    totalCount: number;
  };
  closedIssues: {
    totalCount: number;
  };
  followers: {
    totalCount: number;
  };
  repositories: {
    totalCount: number;
    nodes: Array<{
      name: string;
      stargazers: {
        totalCount: number;
      };
    }>;
    pageInfo: {
      hasNextPage: boolean;
      endCursor: string | null;
    };
  };
}

export interface GitHubGraphQLResponse {
  data: {
    user: GitHubUser;
  };
  errors?: Array<{
    type: string;
    message: string;
  }>;
}
```

**2.2 Create src/types/stats.ts** (Stats Data Model)
```typescript
export interface Rank {
  level: string;
  percentile: number;
}

export interface StatsData {
  name: string;
  totalPRs: number;
  totalPRsMerged: number;
  mergedPRsPercentage: number;
  totalReviews: number;
  totalCommits: number;
  totalIssues: number;
  totalStars: number;
  contributedTo: number;
  rank: Rank;
}

export interface FetchStatsOptions {
  username: string;
  includeAllCommits?: boolean;
  excludeRepos?: string[];
  includeMergedPRs?: boolean;
}
```

**2.3 Create src/types/card.ts** (Card Rendering Options)
```typescript
export interface CardColors {
  titleColor: string;
  textColor: string;
  iconColor: string;
  bgColor: string;
  borderColor: string;
  ringColor: string;
}

export interface RenderOptions {
  hide?: string[];
  showIcons?: boolean;
  hideTitle?: boolean;
  hideBorder?: boolean;
  hideRank?: boolean;
  cardWidth?: number;
  lineHeight?: number;
  theme?: string;
  customTitle?: string;
  borderRadius?: number;
  disableAnimations?: boolean;
  colors?: Partial<CardColors>;
}
```

---

### Step 3: Implement Utilities

**3.1 Create src/utils/http.ts** (HTTP Request Helper)
```typescript
import axios, { AxiosResponse } from 'axios';

const GITHUB_API_URL = 'https://api.github.com/graphql';

export interface GraphQLRequest {
  query: string;
  variables: Record<string, any>;
}

export async function request(
  data: GraphQLRequest,
  headers: Record<string, string>
): Promise<AxiosResponse> {
  return axios.post(GITHUB_API_URL, data, {
    headers: {
      'Content-Type': 'application/json',
      ...headers,
    },
  });
}
```

**3.2 Create src/utils/retryer.ts** (Retry Logic)
```typescript
export async function retryer<T>(
  fetcher: (...args: any[]) => Promise<T>,
  variables: any,
  retries = 3
): Promise<T> {
  let lastError: Error | undefined;
  
  for (let i = 0; i < retries; i++) {
    try {
      return await fetcher(variables);
    } catch (error) {
      lastError = error as Error;
      if (i < retries - 1) {
        await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
      }
    }
  }
  
  throw lastError;
}
```

**3.3 Create src/utils/formatter.ts** (Number Formatting)
```typescript
export function kFormatter(num: number, precision = 1): string {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(precision) + 'm';
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(precision) + 'k';
  }
  return num.toString();
}

export function clampValue(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max);
}
```

**3.4 Create src/utils/rank.ts** (Rank Calculation - simplified version)
```typescript
import { Rank } from '../types/stats';

export function calculateRank(stats: {
  commits: number;
  prs: number;
  issues: number;
  stars: number;
  followers: number;
}): Rank {
  const COMMITS_WEIGHT = 2;
  const PRS_WEIGHT = 3;
  const ISSUES_WEIGHT = 1;
  const STARS_WEIGHT = 4;
  const FOLLOWERS_WEIGHT = 1;

  const totalScore =
    stats.commits * COMMITS_WEIGHT +
    stats.prs * PRS_WEIGHT +
    stats.issues * ISSUES_WEIGHT +
    stats.stars * STARS_WEIGHT +
    stats.followers * FOLLOWERS_WEIGHT;

  const normalizedScore = Math.log10(totalScore + 1);

  let level = 'C';
  let percentile = 100;

  if (normalizedScore > 5) {
    level = 'S+';
    percentile = 1;
  } else if (normalizedScore > 4) {
    level = 'S';
    percentile = 10;
  } else if (normalizedScore > 3.5) {
    level = 'A+';
    percentile = 25;
  } else if (normalizedScore > 3) {
    level = 'A';
    percentile = 40;
  } else if (normalizedScore > 2.5) {
    level = 'B+';
    percentile = 60;
  } else if (normalizedScore > 2) {
    level = 'B';
    percentile = 80;
  }

  return { level, percentile };
}
```

---

### Step 4: Implement GitHub Stats Fetcher

**4.1 Create src/fetchers/stats.ts**
```typescript
import { request } from '../utils/http';
import { retryer } from '../utils/retryer';
import { calculateRank } from '../utils/rank';
import {
  GitHubGraphQLResponse,
  StatsData,
  FetchStatsOptions,
} from '../types';

const STATS_QUERY = `
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
`;

async function fetcher(
  variables: any,
  token: string
): Promise<GitHubGraphQLResponse> {
  const response = await request(
    {
      query: STATS_QUERY,
      variables,
    },
    {
      Authorization: `bearer ${token}`,
    }
  );
  return response.data;
}

export async function fetchStats(
  options: FetchStatsOptions
): Promise<StatsData> {
  const { username, excludeRepos = [] } = options;
  const token = process.env.GITHUB_TOKEN;

  if (!token) {
    throw new Error('GITHUB_TOKEN is required');
  }

  if (!username) {
    throw new Error('Username is required');
  }

  const response = await retryer(
    (vars) => fetcher(vars, token),
    { login: username }
  );

  if (response.errors) {
    const error = response.errors[0];
    throw new Error(error.message || 'Failed to fetch stats');
  }

  const user = response.data.user;

  // Calculate total stars (excluding specified repos)
  const excludeSet = new Set(excludeRepos);
  const totalStars = user.repositories.nodes
    .filter((repo) => !excludeSet.has(repo.name))
    .reduce((sum, repo) => sum + repo.stargazers.totalCount, 0);

  // Build stats object
  const stats: StatsData = {
    name: user.name || user.login,
    totalCommits: user.commits.totalCommitContributions,
    totalPRs: user.pullRequests.totalCount,
    totalPRsMerged: user.mergedPullRequests?.totalCount || 0,
    mergedPRsPercentage:
      user.pullRequests.totalCount > 0
        ? (user.mergedPullRequests!.totalCount / user.pullRequests.totalCount) * 100
        : 0,
    totalReviews: user.reviews.totalPullRequestReviewContributions,
    totalIssues: user.openIssues.totalCount + user.closedIssues.totalCount,
    totalStars,
    contributedTo: user.repositoriesContributedTo.totalCount,
    rank: calculateRank({
      commits: user.commits.totalCommitContributions,
      prs: user.pullRequests.totalCount,
      issues: user.openIssues.totalCount + user.closedIssues.totalCount,
      stars: totalStars,
      followers: user.followers.totalCount,
    }),
  };

  return stats;
}
```

---

### Step 5: Implement SVG Renderer

**5.1 Create src/renderers/card.ts** (Basic Card Template)
```typescript
import { CardColors } from '../types/card';

export function createCard(
  width: number,
  height: number,
  colors: CardColors,
  content: string,
  title: string = '',
  hideTitle: boolean = false,
  hideBorder: boolean = false
): string {
  const borderStyle = hideBorder
    ? ''
    : `stroke="${colors.borderColor}" stroke-width="1"`;

  return `
    <svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" 
         xmlns="http://www.w3.org/2000/svg" role="img">
      <title>${title}</title>
      <rect 
        x="0.5" y="0.5" 
        width="${width - 1}" height="${height - 1}" 
        rx="4.5" 
        fill="${colors.bgColor}" 
        ${borderStyle}
      />
      ${!hideTitle ? `
        <text x="25" y="35" class="header" fill="${colors.titleColor}">
          ${title}
        </text>
      ` : ''}
      ${content}
      <style>
        .header { font: 600 18px 'Segoe UI', Ubuntu, Sans-Serif; }
        .stat { font: 600 14px 'Segoe UI', Ubuntu, Sans-Serif; fill: ${colors.textColor}; }
        .bold { font-weight: 700; }
      </style>
    </svg>
  `;
}
```

**5.2 Create src/renderers/stats-card.ts** (Stats Card Renderer)
```typescript
import { StatsData, RenderOptions, CardColors } from '../types';
import { kFormatter } from '../utils/formatter';
import { createCard } from './card';

const DEFAULT_COLORS: CardColors = {
  titleColor: '#2f80ed',
  textColor: '#434d58',
  iconColor: '#4c71f2',
  bgColor: '#fffefe',
  borderColor: '#e4e2e2',
  ringColor: '#2f80ed',
};

interface StatItem {
  label: string;
  value: string;
  id: string;
}

function createStatItem(item: StatItem, index: number, yOffset: number): string {
  const y = yOffset + index * 25;
  return `
    <g transform="translate(0, ${y})">
      <text class="stat" x="25" y="12.5">${item.label}:</text>
      <text class="stat bold" x="220" y="12.5" data-testid="${item.id}">
        ${item.value}
      </text>
    </g>
  `;
}

function createRankCircle(
  rank: { level: string; percentile: number },
  x: number,
  y: number,
  ringColor: string
): string {
  const progress = 100 - rank.percentile;
  const circumference = 2 * Math.PI * 40;
  const offset = ((100 - progress) / 100) * circumference;

  return `
    <g transform="translate(${x}, ${y})">
      <circle class="rank-circle-rim" cx="0" cy="0" r="40" 
              stroke="${ringColor}" stroke-width="6" fill="none" opacity="0.2"/>
      <circle class="rank-circle" cx="0" cy="0" r="40" 
              stroke="${ringColor}" stroke-width="6" fill="none" 
              stroke-dasharray="${circumference}" 
              stroke-dashoffset="${offset}"
              transform="rotate(-90)" 
              transform-origin="0 0"/>
      <text x="0" y="5" text-anchor="middle" class="stat bold" 
            style="font-size: 24px;">
        ${rank.level}
      </text>
    </g>
  `;
}

export function renderStatsCard(
  stats: StatsData,
  options: RenderOptions = {}
): string {
  const {
    hide = [],
    hideTitle = false,
    hideBorder = false,
    hideRank = false,
    cardWidth = 450,
    customTitle,
    colors: customColors = {},
  } = options;

  const colors = { ...DEFAULT_COLORS, ...customColors };
  const title = customTitle || `${stats.name}'s GitHub Stats`;

  // Define available stats
  const allStats: Record<string, StatItem> = {
    stars: { label: 'Total Stars', value: kFormatter(stats.totalStars), id: 'stars' },
    commits: { label: 'Total Commits', value: kFormatter(stats.totalCommits), id: 'commits' },
    prs: { label: 'Total PRs', value: kFormatter(stats.totalPRs), id: 'prs' },
    issues: { label: 'Total Issues', value: kFormatter(stats.totalIssues), id: 'issues' },
    contribs: { label: 'Contributed to', value: kFormatter(stats.contributedTo), id: 'contribs' },
  };

  // Filter stats based on hide option
  const visibleStats = Object.keys(allStats)
    .filter((key) => !hide.includes(key))
    .map((key) => allStats[key]);

  // Calculate card dimensions
  const statHeight = visibleStats.length * 25;
  const yOffset = hideTitle ? 40 : 60;
  const height = yOffset + statHeight + 30;

  // Render stat items
  const statsContent = visibleStats
    .map((stat, i) => createStatItem(stat, i, yOffset))
    .join('');

  // Render rank circle
  const rankCircle = hideRank
    ? ''
    : createRankCircle(stats.rank, cardWidth - 80, height / 2, colors.ringColor);

  const content = statsContent + rankCircle;

  return createCard(
    cardWidth,
    height,
    colors,
    content,
    title,
    hideTitle,
    hideBorder
  );
}
```

---

### Step 6: Create API Endpoint

**6.1 Create .env file**
```
GITHUB_TOKEN=your_github_personal_access_token_here
PORT=3000
```

**6.2 Create src/api/index.ts** (Express Endpoint)
```typescript
import express, { Request, Response } from 'express';
import dotenv from 'dotenv';
import { fetchStats } from '../fetchers/stats';
import { renderStatsCard } from '../renderers/stats-card';

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

app.get('/api/stats', async (req: Request, res: Response) => {
  const {
    username,
    hide,
    hide_title,
    hide_border,
    hide_rank,
    card_width,
    custom_title,
    theme,
  } = req.query;

  // Set response type to SVG
  res.setHeader('Content-Type', 'image/svg+xml');
  res.setHeader('Cache-Control', 'public, max-age=1800'); // 30 min cache

  try {
    if (!username || typeof username !== 'string') {
      throw new Error('Username is required');
    }

    // Fetch stats
    const stats = await fetchStats({ username });

    // Parse options
    const hideArray = hide
      ? (hide as string).split(',').map((s) => s.trim())
      : [];

    const renderOptions = {
      hide: hideArray,
      hideTitle: hide_title === 'true',
      hideBorder: hide_border === 'true',
      hideRank: hide_rank === 'true',
      cardWidth: card_width ? parseInt(card_width as string, 10) : 450,
      customTitle: custom_title as string | undefined,
    };

    // Render card
    const svg = renderStatsCard(stats, renderOptions);

    res.send(svg);
  } catch (error) {
    const message = error instanceof Error ? error.message : 'Unknown error';
    
    // Return error as SVG
    const errorSvg = `
      <svg width="450" height="120" xmlns="http://www.w3.org/2000/svg">
        <rect width="450" height="120" fill="#fff" stroke="#e4e2e2"/>
        <text x="225" y="60" text-anchor="middle" fill="#d73a4a" 
              style="font: 600 16px 'Segoe UI', Ubuntu, Sans-Serif;">
          Error: ${message}
        </text>
      </svg>
    `;
    
    res.status(500).send(errorSvg);
  }
});

app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
  console.log(`Test: http://localhost:${PORT}/api/stats?username=YOUR_GITHUB_USERNAME`);
});
```

---

### Step 7: Write Tests

**7.1 Create tests/fetchers/stats.test.ts**
```typescript
import { fetchStats } from '../../src/fetchers/stats';

// Mock axios
jest.mock('axios');

describe('fetchStats', () => {
  it('should throw error if username is missing', async () => {
    await expect(fetchStats({ username: '' })).rejects.toThrow('Username is required');
  });

  it('should throw error if GITHUB_TOKEN is missing', async () => {
    delete process.env.GITHUB_TOKEN;
    await expect(fetchStats({ username: 'testuser' })).rejects.toThrow('GITHUB_TOKEN is required');
  });

  // Add more tests with mocked API responses
});
```

**7.2 Create tests/renderers/stats-card.test.ts**
```typescript
import { renderStatsCard } from '../../src/renderers/stats-card';
import { StatsData } from '../../src/types';

describe('renderStatsCard', () => {
  const mockStats: StatsData = {
    name: 'Test User',
    totalCommits: 1000,
    totalPRs: 50,
    totalPRsMerged: 40,
    mergedPRsPercentage: 80,
    totalReviews: 30,
    totalIssues: 20,
    totalStars: 500,
    contributedTo: 10,
    rank: { level: 'A', percentile: 25 },
  };

  it('should render SVG with stats', () => {
    const svg = renderStatsCard(mockStats);
    expect(svg).toContain('<svg');
    expect(svg).toContain('Test User');
    expect(svg).toContain('Total Stars');
  });

  it('should hide stats when specified', () => {
    const svg = renderStatsCard(mockStats, { hide: ['stars'] });
    expect(svg).not.toContain('Total Stars');
  });

  it('should hide title when hideTitle is true', () => {
    const svg = renderStatsCard(mockStats, { hideTitle: true });
    expect(svg).not.toContain('GitHub Stats');
  });
});
```

---

### Step 8: Run and Test

**8.1 Build the project**
```bash
npm run build
```

**8.2 Start the development server**
```bash
npm run dev
```

**8.3 Test the endpoint**
Open in browser:
```
http://localhost:3000/api/stats?username=YOUR_GITHUB_USERNAME
```

**8.4 Run tests**
```bash
npm test
```

---

### Step 9: Deploy (Optional - Vercel)

**9.1 Create vercel.json**
```json
{
  "functions": {
    "api/**/*.ts": {
      "memory": 128,
      "maxDuration": 10
    }
  }
}
```

**9.2 Deploy**
```bash
npm install -g vercel
vercel
```

---

## Key Differences from Original

1. **TypeScript**: Full type safety with interfaces
2. **Simplified**: Focuses only on Stats card (no gist/wakatime/etc.)
3. **Modular**: Clear separation of concerns (fetcher, renderer, utils)
4. **Modern**: Uses async/await, ES6+ features
5. **Testable**: Designed for easy unit testing

## Next Steps for Enhancement

1. Add theme support (multiple color schemes)
2. Add i18n (translations)
3. Add more card customization options
4. Implement caching layer (Redis/memory cache)
5. Add rate limiting
6. Support all stats from original (discussions, merged PRs, etc.)

## Troubleshooting

**Issue**: "GITHUB_TOKEN is required"
- Create a GitHub Personal Access Token at https://github.com/settings/tokens
- Add `read:user` and `repo` permissions
- Add to `.env` file

**Issue**: GraphQL errors
- Check token permissions
- Verify username exists
- Check GitHub API status

---

## Resources

- [GitHub GraphQL API Docs](https://docs.github.com/en/graphql)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/handbook/intro.html)
- [Original Project](https://github.com/anuraghazra/github-readme-stats)


