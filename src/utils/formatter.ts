// Number Formatting

export function kFormatter(num: number, precision: number = 1): string {
	if (num >= 1000000) {
		return (num / 1000000).toFixed(precision) + "m";
	}

	if (num >= 1000) {
		return (num / 1000).toFixed(precision) + "k";
	}

	return num.toString();
}

export function clampValue(value: number, min: number, max: number): number {
	return Math.min(Math.max(value, min), max);
}
