import { renderStatsCard } from "../../renderers/stats-card";
import { StatsData } from "../../types/stats";

describe("renderStatsCard", () => {
	const mockStats: StatsData = {
		name: "Test User",
		totalCommits: 1000,
		totalPRs: 50,
		totalPRsMerged: 40,
		mergedPRsPercentage: 80,
		totalReviews: 30,
		totalIssues: 20,
		totalStars: 500,
		contributedTo: 10,
		rank: { level: "A", percentile: 25 },
	};

	it("should render SVG with stats", () => {
		const svg = renderStatsCard(mockStats);
		expect(svg).toContain("<svg");
		expect(svg).toContain("Test User");
		expect(svg).toContain("Total Stars");
	});

	it("should hide stats when specified", () => {
		const svg = renderStatsCard(mockStats, { hide: ["stars"] });
		expect(svg).not.toContain("Total Stars");
	});

	it("should hide title when hideTitle is true", () => {
		const svg = renderStatsCard(mockStats, { hideTitle: true });
		expect(svg).not.toContain("GitHub Stats");
	});
});
