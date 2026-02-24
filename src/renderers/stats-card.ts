import { CardColors, RenderOptions } from "../types/card";
import { StatsData } from "../types/stats";
import { kFormatter } from "../utils/formatter";
import { createCard } from "./card";

const DEFAULT_COLORS: CardColors = {
	titleColor: "#2f80ed",
	textColor: "#434d58",
	iconColor: "#4c71f2",
	bgColor: "#fffefe",
	borderColor: "#e4e2e2",
	ringColor: "#2f80ed",
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

function createRankCircle(rank: { level: string; percentile: number }, x: number, y: number, circleRadius: number = 40, ringColor: string): string {
	const progress = 100 - rank.percentile; // 100% - percentile to show remaining part
	const circumference = 2 * Math.PI * circleRadius;
	const offset = circumference * (1 - progress / 100) * circumference;

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

export function renderStatsCard(stats: StatsData, options: RenderOptions = {}): string {
	const { hide = [], hideTitle = false, hideBorder = false, hideRank = false, cardWidth = 450, customTitle, colors: customColors = {} } = options;

	const colors = { ...DEFAULT_COLORS, ...customColors };
	const title = customTitle || `${stats.name}'s GitHub Stats`;

	// Prepare stat items
	const allStats: Record<string, StatItem> = {
		stars: {
			label: "Total Stars",
			value: kFormatter(stats.totalStars),
			id: "stars",
		},
		commits: {
			label: "Total Commits",
			value: kFormatter(stats.totalCommits),
			id: "commits",
		},
		prs: {
			label: "Total PRs",
			value: kFormatter(stats.totalPRs),
			id: "prs",
		},
		issues: {
			label: "Total Issues",
			value: kFormatter(stats.totalIssues),
			id: "issues",
		},
		contribs: {
			label: "Total Contributions",
			value: kFormatter(stats.contributedTo),
			id: "contribs",
		},
	};

	// オプションに基づいて表示する統計項目をフィルタリング
	const visibleStats = Object.keys(allStats)
		.filter((key) => !hide.includes(key))
		.map((key) => allStats[key]);

	// カードの高さを動的に計算
	const statHeight = Object.keys(allStats).length * 25; // 各統計項目の高さ
	const yOffset = hideTitle ? 20 : 50; // タイトルの有無に応じたオフセット
	const height = yOffset + statHeight + 20; // 上下の余白を追加

	// StatItem を SVG 要素に変換
	const statsContent = visibleStats.map((item, index) => createStatItem(item, index, yOffset)).join("");

	// ランクサークルの生成
	const rankCircle = hideRank ? "" : createRankCircle(stats.rank, cardWidth - 80, height / 2, 40, colors.ringColor);
	const content = statsContent + rankCircle;

	return createCard(cardWidth, height, colors, content, title, hideTitle, hideBorder);
}