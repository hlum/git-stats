export async function retryer<T>(fetcher: (...args: any[]) => Promise<T>, variables: any[], maxRetry: number = 3): Promise<T> {
	let lastError: Error | undefined;

	for (let i = 0; i < maxRetry; i++) {
		try {
			return await fetcher(...variables);
		} catch (error) {
			lastError = error as Error;
			if (i < maxRetry - 1) {
				await new Promise((resolve) => setTimeout(resolve, 1000 * (i + 1))); // Exponential backoff: wait 1s, then 2s, then 3s, etc.
			}
		}
	}
	throw lastError;
}
