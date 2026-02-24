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