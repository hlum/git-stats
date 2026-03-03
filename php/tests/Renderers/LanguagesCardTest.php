<?php

declare(strict_types=1);

namespace Tests\Renderers;

use App\Renderers\LanguagesCard;
use App\Types\CardColors;
use App\Types\RenderOptions;
use App\Types\Theme;
use PHPUnit\Framework\TestCase;

class LanguagesCardTest extends TestCase
{
    private array $mockLanguages;

    protected function setUp(): void
    {
        $this->mockLanguages = [
            'TypeScript' => ['size' => 50000, 'color' => '#3178c6', 'percentage' => 50.0],
            'JavaScript' => ['size' => 30000, 'color' => '#f1e05a', 'percentage' => 30.0],
            'PHP' => ['size' => 15000, 'color' => '#4F5D95', 'percentage' => 15.0],
            'CSS' => ['size' => 5000, 'color' => '#563d7c', 'percentage' => 5.0],
        ];
    }

    public function testRendersLanguagesWithSvg(): void
    {
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('TypeScript', $svg);
        $this->assertStringContainsString('JavaScript', $svg);
        $this->assertStringContainsString('Most Used Languages', $svg);
    }

    public function testLimitsLanguageCount(): void
    {
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser', null, 2);

        $this->assertStringContainsString('TypeScript', $svg);
        $this->assertStringContainsString('JavaScript', $svg);
        $this->assertStringNotContainsString('PHP', $svg);
        $this->assertStringNotContainsString('CSS', $svg);
    }

    public function testAppliesDarkTheme(): void
    {
        $colors = Theme::getColors('dark');
        $options = new RenderOptions(colors: $colors);
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser', $options);

        // Dark theme uses #0d1117 as background
        $this->assertStringContainsString('#0d1117', $svg);
    }

    public function testAppliesLightTheme(): void
    {
        $colors = Theme::getColors('light');
        $options = new RenderOptions(colors: $colors);
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser', $options);

        // Light theme uses #fffefe as background
        $this->assertStringContainsString('#fffefe', $svg);
    }

    public function testAppliesCustomColors(): void
    {
        $colors = new CardColors(bgColor: '#ff0000', textColor: '#00ff00');
        $options = new RenderOptions(colors: $colors);
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser', $options);

        $this->assertStringContainsString('#ff0000', $svg);
        $this->assertStringContainsString('#00ff00', $svg);
    }

    public function testHidesTitleWhenRequested(): void
    {
        $options = new RenderOptions(hideTitle: true);
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser', $options);

        $this->assertStringNotContainsString('class="header"', $svg);
    }

    public function testUsesCustomTitle(): void
    {
        $options = new RenderOptions(customTitle: 'My Languages');
        $svg = LanguagesCard::render($this->mockLanguages, 'testuser', $options);

        $this->assertStringContainsString('My Languages', $svg);
    }
}
