import { GitHubGraphQLResponse } from "../types/github";
import { FetchStatsOptions, StatsData } from "../types/stats";
import { request } from "../utils/http";
import { calculateRank } from "../utils/rank";
import { retryer } from "../utils/retryer";

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

async function fetcher(variables: any, token: string): Promise<GitHubGraphQLResponse> {
	const response = await request(
		{
			query: STATS_QUERY,
			variables,
		},
		{
			Authorization: `bearer ${token}`,
		},
	);

	return response.data;
}

export async function fetchStats(options: FetchStatsOptions): Promise<StatsData> {
	const { username, excludeRepos = [] } = options;
	const token = process.env.GITHUB_TOKEN;

	if (!token) {
		throw new Error("GITHUB_TOKEN is not set in environment variables.");
	}

	if (!username) {
		throw new Error("Username is required to fetch stats.");
	}

	const response = await retryer((vars) => fetcher(vars, token), { login: username });

	if (response.errors) {
		const error = response.errors[0];
		throw new Error(error.message || "Failed to fetch stats.");
	}

	const user = response.data.user;

	// Calculate total stars (指定されたリポジトリを除外)
	const excludeSet = new Set(excludeRepos);
	const totalStars = user.repositories.nodes.filter((repo) => !excludeSet.has(repo.name)).reduce((sum, repo) => sum + repo.stargazers.totalCount, 0);

	// Calculate stats Object (コミット数、PR数、Issue数、スター数、フォロワー数)
	const stats = {
		name: user.name || user.login,
		totalCommits: user.commits.totalCommitContributions,
		totalPRs: user.pullRequests.totalCount,
		totalPRsMerged: user.mergedPullRequests.totalCount,
		mergedPRsPercentage: user.pullRequests.totalCount > 0 ? (user.mergedPullRequests.totalCount / user.pullRequests.totalCount) * 100 : 0,
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
