export interface CardColors {
	titleColor: string;
	textColor: string;
	iconColor: string;
	bgColor: string;
	borderColor: string;
	ringColor: string;
}

export interface RenderOptions {
	hide?: string[];
	showIcons?: boolean;
	hideTitle?: boolean;
	hideBorder?: boolean;
	hideRank?: boolean;
	cardWidth?: number;
	lineHeight?: number;
	theme?: string;
	customTitle?: string;
	borderRadius?: number;
	disableAnimations?: boolean;
	colors?: Partial<CardColors>;
}
