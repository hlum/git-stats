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
	mergedPullRequests: {
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
			endCursor: string;
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
