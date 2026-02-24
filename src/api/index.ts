import dotenv from "dotenv";
import express, { Request, Response } from "express";
import { fetchStats } from "../fetchers/stats";
import { renderStatsCard } from "../renderers/stats-card";

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

app.get("/api/stats", async (req: Request, res: Response) => {
	const { username, hide, hide_title, hide_border, hide_rank, card_width, custom_title, theme } = req.query;

	// ResponseタイプをSVGに設定
	res.setHeader("Content-Type", "image/svg+xml");
	res.setHeader("Cache-Control", "public, max-age=1800"); // 30分キャッシュ

	try {
		if (!username || typeof username !== "string") {
			throw new Error("Username is required and must be a string");
		}

		// ユーザーデータの取得
		const stats = await fetchStats({ username });

		// Optionsのパース
		const hideArray = hide ? (hide as string).split(",").map((item) => item.trim()) : [];

		const renderOptions = {
			hide: hideArray,
			hideTitle: hide_title === "true",
			hideBorder: hide_border === "true",
			hideRank: hide_rank === "true",
			cardWidth: card_width ? parseInt(card_width as string, 10) : 450,
			customTitle: custom_title as string | undefined,
		};

		// Render card
		const svg = renderStatsCard(stats, renderOptions);

		res.send(svg);
	} catch (error) {
		const message = error instanceof Error ? error.message : "Unknown error";

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
