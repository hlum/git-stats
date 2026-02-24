import { CardColors } from "../types/card";

export function createCard(width: number, height: number, colors: CardColors, content: string, title: string = "", hideTitle: boolean = false, hideBorder: boolean = false): string {
	const borderStyle = hideBorder ? "" : `stroke="${colors.borderColor}" stroke-width="1"`;

	return `
    <svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" 
         xmlns="http://www.w3.org/2000/svg" role="img">
      <title>${title}</title>
      <rect 
        x="0.5" y="0.5" 
        width="${width - 1}" height="${height - 1}" 
        rx="4.5" 
        fill="${colors.bgColor}" 
        ${borderStyle}
      />
      ${
			!hideTitle
				? `
        <text x="25" y="35" class="header" fill="${colors.titleColor}">
          ${title}
        </text>
      `
				: ""
		}
      ${content}
      <style>
        .header { font: 600 18px 'Segoe UI', Ubuntu, Sans-Serif; }
        .stat { font: 600 14px 'Segoe UI', Ubuntu, Sans-Serif; fill: ${colors.textColor}; }
        .bold { font-weight: 700; }
      </style>
    </svg>
  `;
}
