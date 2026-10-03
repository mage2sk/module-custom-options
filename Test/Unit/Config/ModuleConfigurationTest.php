<?php
declare(strict_types=1);

namespace Panth\CustomOptions\Test\Unit\Config;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Config\Dom;
use Magento\Framework\Module\Declaration\Converter\Dom as ModuleDeclarationConverter;
use PHPUnit\Framework\TestCase;

class ModuleConfigurationTest extends TestCase
{
    private const MODULE = 'Panth_CustomOptions';

    private string $moduleDir;

    protected function setUp(): void
    {
        $path = (new ComponentRegistrar())->getPath(ComponentRegistrar::MODULE, self::MODULE);
        $this->assertNotNull($path, 'Panth_CustomOptions is not registered');
        $this->moduleDir = realpath($path);
    }

    private function load(string $relative): \DOMDocument
    {
        $file = $this->moduleDir . '/' . $relative;
        $this->assertFileExists($file);
        $dom = new \DOMDocument();
        $this->assertTrue($dom->load($file), $relative . ' is not well formed');
        return $dom;
    }

    private function assertSchemaValid(string $relative, string $schemaUrn): \DOMDocument
    {
        $dom = $this->load($relative);
        $errors = Dom::validateDomDocument($dom, $schemaUrn);
        $this->assertSame([], array_map('strval', $errors), $relative . ' violates ' . $schemaUrn);
        return $dom;
    }

    private function xpath(\DOMDocument $dom, string $query): array
    {
        $result = [];
        foreach ((new \DOMXPath($dom))->query($query) as $node) {
            $result[] = trim($node->nodeValue);
        }
        return $result;
    }

    public function testRegistrationPointsToModuleRoot(): void
    {
        $this->assertSame(realpath(dirname(__DIR__, 3)), $this->moduleDir);
        $this->assertFileExists($this->moduleDir . '/registration.php');
    }

    public function testModuleDeclarationLoadsAfterCatalogAndCore(): void
    {
        $dom = $this->assertSchemaValid('etc/module.xml', 'urn:magento:framework:Module/etc/module.xsd');
        $modules = (new ModuleDeclarationConverter())->convert($dom);

        $this->assertArrayHasKey(self::MODULE, $modules);
        $this->assertSame(self::MODULE, $modules[self::MODULE]['name']);
        $this->assertSame(['Magento_Catalog', 'Panth_Core'], $modules[self::MODULE]['sequence']);
    }

    public function testFrontendDiRegistersThemeConfigVariables(): void
    {
        $dom = $this->assertSchemaValid('etc/frontend/di.xml', 'urn:magento:framework:ObjectManager/etc/config.xsd');

        $items = $this->xpath(
            $dom,
            '/config/type[@name="Panth\Core\ViewModel\ThemeConfig"]'
            . '/arguments/argument[@name="registeredModules"]/item[@name="' . self::MODULE . '"]'
        );
        $this->assertSame([self::MODULE], $items);
        $this->assertSame([], $this->xpath($dom, '/config/preference'));
    }

    public function testStylingIsEnabledByDefault(): void
    {
        $dom = $this->assertSchemaValid('etc/config.xml', 'urn:magento:module:Magento_Store:etc/config.xsd');

        $this->assertSame(['1'], $this->xpath($dom, '/config/default/panth_customoptions/general/enabled'));
    }

    public function testAdminSectionIsGuardedByOwnAclResource(): void
    {
        $system = $this->assertSchemaValid(
            'etc/adminhtml/system.xml',
            'urn:magento:module:Magento_Config:etc/system_file.xsd'
        );
        $acl = $this->assertSchemaValid('etc/acl.xml', 'urn:magento:framework:Acl/etc/acl.xsd');

        $section = '/config/system/section[@id="panth_customoptions"]';
        $this->assertSame(['panth'], $this->xpath($system, $section . '/tab'));
        $this->assertSame(['Panth_CustomOptions::config'], $this->xpath($system, $section . '/resource'));
        $this->assertSame(
            ['Panth_CustomOptions::config'],
            $this->xpath($acl, '//resource[@id="Magento_Config::config"]/resource/@id')
        );

        $field = $section . '/group[@id="general"]/field[@id="enabled"]';
        $this->assertSame([Yesno::class], $this->xpath($system, $field . "/source_model"));
        $this->assertTrue(class_exists(Yesno::class));
        foreach (['showInDefault', 'showInWebsite', 'showInStore'] as $scope) {
            $this->assertSame(['1'], $this->xpath($system, $field . '/@' . $scope), $scope);
        }
    }

    private function templateActions(string $layout): array
    {
        $dom = $this->assertSchemaValid(
            'view/frontend/layout/' . $layout,
            'urn:magento:framework:View/Layout/etc/page_configuration.xsd'
        );
        $this->assertSame([], $this->xpath($dom, '//referenceBlock/@template'), $layout . ' bypasses ifconfig');

        $map = [];
        foreach ((new \DOMXPath($dom))->query('//referenceBlock/action[@method="setTemplate"]') as $action) {
            $this->assertSame('panth_customoptions/general/enabled', $action->getAttribute('ifconfig'));
            $map[$action->parentNode->getAttribute('name')] = trim($action->textContent);
        }
        return $map;
    }

    private function assertTemplatesShipped(array $map): void
    {
        foreach ($map as $block => $template) {
            $this->assertStringStartsWith(self::MODULE . '::', $template, $block);
            $file = $this->moduleDir . '/view/frontend/templates/' . substr($template, strlen(self::MODULE) + 2);
            $this->assertFileExists($file, $block . ' template missing');
        }
    }

    public function testLumaLayoutOverridesEveryOptionRendererWhenEnabled(): void
    {
        $map = $this->templateActions('catalog_product_view.xml');

        $prefix = self::MODULE . '::product/view/options/luma/';
        $this->assertSame(
            [
                'product.info.options' => $prefix . 'options.phtml',
                'product.info.options.text' => $prefix . 'type/text.phtml',
                'product.info.options.select' => $prefix . 'type/select.phtml',
                'product.info.options.file' => $prefix . 'type/file.phtml',
                'product.info.options.date' => $prefix . 'type/date.phtml',
            ],
            $map
        );
        $this->assertTemplatesShipped($map);
    }

    public function testHyvaLayoutOverridesEveryOptionRendererWhenEnabled(): void
    {
        $map = $this->templateActions('hyva_catalog_product_view.xml');

        $prefix = self::MODULE . '::product/view/options/';
        $this->assertSame(
            [
                'product.info.options' => $prefix . 'options.phtml',
                'product.info.options.text' => $prefix . 'type/text.phtml',
                'product.info.options.select' => $prefix . 'type/select.phtml',
                'product.info.options.file' => $prefix . 'type/file.phtml',
                'product.info.options.date' => $prefix . 'type/date-html5.phtml',
            ],
            $map
        );
        $this->assertTemplatesShipped($map);
    }

    public function testEveryShippedTemplateIsReferencedByALayout(): void
    {
        $referenced = array_merge(
            array_values($this->templateActions('catalog_product_view.xml')),
            array_values($this->templateActions('hyva_catalog_product_view.xml'))
        );
        $base = $this->moduleDir . '/view/frontend/templates/';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );
        $shipped = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'phtml') {
                $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($base)));
                $shipped[] = self::MODULE . '::' . $relative;
            }
        }
        sort($shipped);
        sort($referenced);
        $this->assertSame($referenced, $shipped);
    }

    public function testHyvaLayoutLoadsShippedStylesheet(): void
    {
        $dom = $this->assertSchemaValid(
            'view/frontend/layout/hyva_catalog_product_view.xml',
            'urn:magento:framework:View/Layout/etc/page_configuration.xsd'
        );

        $sources = $this->xpath($dom, '/page/head/css/@src');
        $this->assertSame([self::MODULE . '::css/hyva-options.css'], $sources);
        $this->assertFileExists($this->moduleDir . '/view/frontend/web/css/hyva-options.css');
    }

    public function testEnableFieldExplainsWhatItSwitches(): void
    {
        $comment = $this->xpath(
            $this->load('etc/adminhtml/system.xml'),
            '/config/system/section[@id="panth_customoptions"]/group[@id="general"]/field[@id="enabled"]/comment'
        );
        $this->assertCount(1, $comment);
        $this->assertStringContainsString('Hyva and Luma', $comment[0]);
        $this->assertStringContainsString('active theme', $comment[0]);
    }

    public function testLumaTemplatesInitialiseShippedScripts(): void
    {
        $templates = $this->moduleDir . '/view/frontend/templates/product/view/options/luma/type/';
        $scripts = [
            'select.phtml' => 'Panth_CustomOptions/js/card-options',
            'date.phtml' => 'Panth_CustomOptions/js/date-labels',
        ];
        foreach ($scripts as $template => $component) {
            $this->assertStringContainsString($component, (string) file_get_contents($templates . $template));
            $this->assertFileExists(
                $this->moduleDir . '/view/frontend/web/' . substr($component, strlen(self::MODULE) + 1) . '.js'
            );
        }
    }

    public function testChoiceGroupsAndDateFieldsAreLabelledAsGroups(): void
    {
        $base = $this->moduleDir . '/view/frontend/templates/product/view/options/';
        foreach (['type/select.phtml', 'luma/type/select.phtml'] as $template) {
            $source = (string) file_get_contents($base . $template);
            $this->assertStringContainsString('radiogroup', $source, $template);
            $this->assertStringContainsString('aria-labelledby', $source, $template);
        }
        $date = (string) file_get_contents($base . 'luma/type/date.phtml');
        $this->assertStringContainsString('aria-labelledby', $date);
        $this->assertStringNotContainsString('<label class="panth-option-label">', $date);
    }

    public function testHyvaFileOptionOffersAccessibleRemoveControl(): void
    {
        $source = (string) file_get_contents(
            $this->moduleDir . '/view/frontend/templates/product/view/options/type/file.phtml'
        );
        $this->assertStringContainsString('class="panth-file-remove"', $source);
        $this->assertStringContainsString("escapeHtmlAttr(__('Remove file'))", $source);
        $this->assertStringContainsString('resetFile()', $source);
    }
}
