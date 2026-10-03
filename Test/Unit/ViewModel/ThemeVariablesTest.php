<?php
declare(strict_types=1);

namespace Panth\CustomOptions\Test\Unit\ViewModel;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\DesignInterface;
use Panth\Core\ViewModel\ThemeConfig;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ThemeVariablesTest extends TestCase
{
    private const MODULE = 'Panth_CustomOptions';

    private const COLOR_KEYS = [
        'custom-options-primary',
        'custom-options-border',
        'custom-options-border-focus',
        'custom-options-bg',
        'custom-options-input-bg',
        'custom-options-text',
        'custom-options-text-muted',
        'custom-options-label',
        'custom-options-price',
        'custom-options-required',
    ];

    private function themeConfig(?LoggerInterface $logger = null): ThemeConfig
    {
        $etcDir = (new ComponentRegistrar())->getPath(ComponentRegistrar::MODULE, self::MODULE) . '/etc';
        $reader = $this->createMock(ModuleDirReader::class);
        $reader->expects($this->once())
            ->method('getModuleDir')
            ->with(Dir::MODULE_ETC_DIR, self::MODULE)
            ->willReturn($etcDir);
        $design = $this->createStub(DesignInterface::class);
        $design->method('getDesignTheme')->willReturn(null);

        return new ThemeConfig(
            $reader,
            new Json(),
            $logger ?? $this->createStub(LoggerInterface::class),
            $design,
            $this->createStub(ComponentRegistrar::class),
            [self::MODULE => self::MODULE]
        );
    }

    private function luminance(string $hex): float
    {
        $channels = array_map(
            static function (string $pair): float {
                $c = hexdec($pair) / 255;
                return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
            },
            str_split(ltrim($hex, '#'), 2)
        );
        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function contrast(string $foreground, string $background): float
    {
        $a = $this->luminance($foreground);
        $b = $this->luminance($background);
        return (max($a, $b) + 0.05) / (min($a, $b) + 0.05);
    }

    public function testShippedVariablesAreExposedAsCssCustomProperties(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('warning');
        $css = $this->themeConfig($logger)->getCssVariables();

        $this->assertStringStartsWith(":root {\n", $css);
        $this->assertStringEndsWith("}\n", $css);
        $this->assertStringContainsString("  --custom-options-primary: #0F766E;\n", $css);
        $this->assertStringContainsString("  --custom-options-radius: 10px;\n", $css);
        $this->assertStringContainsString("  --custom-options-font: inherit;\n", $css);
        $this->assertSame(12, substr_count($css, '--custom-options-'));
        $this->assertSame(12, substr_count($css, '  --'));
    }

    public function testEveryColourTokenIsAValidHexValue(): void
    {
        $config = $this->themeConfig();
        foreach (self::COLOR_KEYS as $key) {
            $value = $config->getValue($key);
            $this->assertNotNull($value, $key);
            $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/', $value, $key);
        }
        $this->assertMatchesRegularExpression('/^\d+px$/', (string) $config->getValue('custom-options-radius'));
        $this->assertSame('fallback', $config->getValue('custom-options-missing', 'fallback'));
    }

    public function testTextTokensMeetWcagAaContrast(): void
    {
        $config = $this->themeConfig();
        $surfaces = [$config->getValue('custom-options-bg'), $config->getValue('custom-options-input-bg')];
        $pairs = ['custom-options-text', 'custom-options-text-muted', 'custom-options-label',
            'custom-options-price', 'custom-options-required', 'custom-options-primary'];

        foreach ($pairs as $key) {
            foreach ($surfaces as $surface) {
                $ratio = $this->contrast($config->getValue($key), $surface);
                $this->assertGreaterThanOrEqual(4.5, $ratio, sprintf('%s on %s is %.2f:1', $key, $surface, $ratio));
            }
        }

        $badge = $this->contrast('#FFFFFF', $config->getValue('custom-options-primary'));
        $this->assertGreaterThanOrEqual(4.5, $badge, 'white price badge text on primary');
    }

    public function testFocusBorderIsDistinguishableFromIdleBorder(): void
    {
        $config = $this->themeConfig();
        $bg = $config->getValue('custom-options-input-bg');

        $this->assertNotSame($config->getValue('custom-options-border'), $config->getValue('custom-options-border-focus'));
        $this->assertGreaterThanOrEqual(
            3.0,
            $this->contrast($config->getValue('custom-options-border-focus'), $bg),
            'focus indicator needs 3:1 against the input background'
        );
    }
}
