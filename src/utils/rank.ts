import { Rank } from "../types/stats";

export function calculateRank(stats: { commits: number; prs: number; issues: number; stars: number; followers: number }): Rank {
	const COMMITS_WEIGHT = 2;
	const PRS_WEIGHT = 3;
	const ISSUES_WEIGHT = 1;
	const STARS_WEIGHT = 4;
	const FOLLOWERS_WEIGHT = 1;

	const totalScore = stats.commits * COMMITS_WEIGHT + stats.prs * PRS_WEIGHT + stats.issues * ISSUES_WEIGHT + stats.stars * STARS_WEIGHT + stats.followers * FOLLOWERS_WEIGHT;

	const normalizedScore = Math.log10(totalScore + 1); // Logarithmic scaling to prevent extreme values

	let level = "C";
	let percentile = 0;

	if (normalizedScore > 5) {
		level = "S+";
		percentile = 1;
	} else if (normalizedScore > 4) {
		level = "S";
		percentile = 10;
	} else if (normalizedScore > 3.5) {
		level = "A+";
		percentile = 25;
	} else if (normalizedScore > 3) {
		level = "A";
		percentile = 40;
	} else if (normalizedScore > 2.5) {
		level = "B+";
		percentile = 60;
	} else if (normalizedScore > 2) {
		level = "B";
		percentile = 80;
	}

	return { level, percentile };
}
