# GitHub Stats Card - PHP

Generate GitHub README stats cards as SVG images.

## Quick Start

```bash
# Install dependencies
composer install

# Copy environment file and add your GitHub token
cp .env.example .env
# Edit .env and add your GITHUB_TOKEN

# Start development server
composer dev
```

Server runs at `http://localhost:3000`

## API Endpoints

### Stats Card

```
GET /api/stats?username=USERNAME
```

![Stats Card Example](https://github-readme-stats.vercel.app/api?username=anuraghazra)

### Top Languages Card

```
GET /api/top-langs?username=USERNAME
```

![Languages Card Example](https://github-readme-stats.vercel.app/api/top-langs?username=anuraghazra)

## Parameters

### Common Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `username` | required | GitHub username |
| `theme` | `light` | Color theme: `light` or `dark` |
| `custom_title` | auto | Custom card title |
| `hide_title` | `false` | Hide the title |
| `hide_border` | `false` | Hide the card border |
| `card_width` | `450` | Card width in pixels |
| `card_height` | auto | Card height (auto-calculated if not set) |

### Stats Card Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `hide` | none | Comma-separated stats to hide: `stars,commits,prs,issues,contribs` |
| `hide_rank` | `false` | Hide the rank circle |

### Languages Card Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `langs_count` | `5` | Number of languages to show (1-10) |

### Layout Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `border_radius` | `5` | Corner radius in pixels |
| `line_height` | `25` | Space between rows |
| `padding_x` | `25` | Horizontal padding |
| `padding_y` | `20` | Vertical padding |
| `title_offset_y` | `35` | Title vertical position |
| `title_font_size` | `18` | Title font size |
| `text_font_size` | `14` | Body text font size |

### Color Parameters

All color parameters accept:
- Hex colors: `ff0000` or `#ff0000`
- Named colors: `github-dark`, `dracula-bg`, `red`, etc.

| Parameter | Description |
|-----------|-------------|
| `bg_color` | Background color |
| `title_color` | Title text color |
| `text_color` | Body text color |
| `icon_color` | Icon color |
| `border_color` | Border color |
| `ring_color` | Rank circle color |

### Available Named Colors

#### Basic Colors
`white`, `black`, `red`, `green`, `blue`, `yellow`, `cyan`, `magenta`, `orange`, `purple`, `pink`, `gray`

#### GitHub Colors
`github-dark`, `github-light`, `github-blue`, `github-green`, `github-red`, `github-yellow`, `github-purple`, `github-border-dark`, `github-border-light`, `github-text-dark`, `github-text-light`

#### Theme Colors
- **Dracula**: `dracula-bg`, `dracula-text`, `dracula-pink`, `dracula-purple`, `dracula-cyan`, `dracula-green`
- **Monokai**: `monokai-bg`, `monokai-text`, `monokai-pink`, `monokai-green`, `monokai-yellow`, `monokai-blue`
- **Nord**: `nord-bg`, `nord-text`, `nord-blue`, `nord-green`, `nord-red`
- **Solarized**: `solarized-dark-bg`, `solarized-light-bg`, `solarized-blue`, `solarized-cyan`, `solarized-green`

#### Special
`transparent`, `none`

## Examples

### Dark Theme
```
/api/stats?username=octocat&theme=dark
```

### Custom Colors
```
/api/stats?username=octocat&bg_color=dracula-bg&title_color=dracula-pink&text_color=dracula-text
```

### Hide Stats
```
/api/stats?username=octocat&hide=stars,issues&hide_rank=true
```

### Custom Layout
```
/api/stats?username=octocat&card_width=500&border_radius=15&title_font_size=20
```

### Languages with Custom Count
```
/api/top-langs?username=octocat&langs_count=8&theme=dark
```

### Fully Customized
```
/api/stats?username=octocat&bg_color=github-dark&title_color=github-blue&text_color=github-text-dark&border_color=github-border-dark&border_radius=10&card_width=400
```

## Use in GitHub README

```markdown
![GitHub Stats](http://localhost:3000/api/stats?username=YOUR_USERNAME&theme=dark)

![Top Languages](http://localhost:3000/api/top-langs?username=YOUR_USERNAME&theme=dark)
```

## Development

```bash
# Run tests
composer test

# Run single test
./vendor/bin/phpunit --filter=StatsCard
```

## Requirements

- PHP 8.1+
- Composer
- GitHub Personal Access Token with `read:user` and `repo` permissions
