import { fetchStats } from "../../fetchers/stats";

// Mock axios
jest.mock("axios");

describe("fetchStats", () => {
	it("should throw error if username is missing", async () => {
		await expect(fetchStats({ username: "" })).rejects.toThrow("Username is required");
	});

	it("should throw error if GITHUB_TOKEN is missing", async () => {
		delete process.env.GITHUB_TOKEN;
		await expect(fetchStats({ username: "testuser" })).rejects.toThrow("GITHUB_TOKEN is required");
	});

	// Add more tests with mocked API responses
});
