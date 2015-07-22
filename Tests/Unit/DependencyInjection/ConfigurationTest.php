<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Cmf\Bundle\SeoBundle\DependencyInjection\CmfSeoExtension;
use Symfony\Cmf\Bundle\SeoBundle\DependencyInjection\Configuration;
use Symfony\Cmf\Bundle\SeoBundle\SeoPresentation;

/**
 * This test will try to cover all configs.
 *
 * Means check if all available formats are equal.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new CmfSeoExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testDefaultsForAllConfigFormats()
    {
        $expectedConfiguration = array(
            'title'                  => 'default_title',
            'description'            => 'default_description',
            'persistence' => array(
                'phpcr' => array(
                    'enabled' => false,
                    'manager_name' => null,
                ),
                'orm' => array(
                    'enabled' => false,
                    'manager_name' => null,
                ),
            ),
            'translation_domain'     => 'messages',
            'original_route_pattern' => SeoPresentation::ORIGINAL_URL_CANONICAL,
            'sonata_admin_extension' => array(
                'enabled' => 'auto',
                'form_group' => 'form.group_seo',
            ),
            'alternate_locale' => array(
                'enabled'  => false,
                'provider_id' => null,
            ),
            'sitemap' => array(
                'enabled'        => false,
                'configurations' => array(),
                'defaults' => array(
                    'default_change_frequency' => 'always',
                    'templates' => array(),
                ),
            ),
            'content_listener' => array(
                'enabled'     => true,
                'content_key' => 'contentDocument'
            ),
        );

        $sources = array_map(function ($path) {
            return __DIR__.'/../../Resources/Fixtures/'.$path;
        }, array(
            'config/config.yml',
            'config/config.php',
            'config/config.xml',
        ));

        $this->assertProcessedConfigurationEquals($expectedConfiguration, $sources);
    }

    public function testAdvancedXmlConfigurations()
    {
        $expectedConfiguration = array(
            'sitemap' => array(
                'enabled'        => true,
                'configurations' => array(
                    'default' => array(
                        'default_change_frequency' => 'never',
                        'templates' => array(
                            'xml'  => 'test.xml',
                            'html' => 'test.html',
                        ),
                    ),
                ),
                'defaults' => array(
                    'default_change_frequency' => 'always',
                    'templates' => array(),
                ),
            ),
            'persistence' => array(
                'phpcr' => array(
                    'enabled' => false,
                    'manager_name' => null,
                ),
                'orm' => array(
                    'enabled' => false,
                    'manager_name' => null,
                ),
            ),
            'translation_domain'     => 'messages',
            'original_route_pattern' => SeoPresentation::ORIGINAL_URL_CANONICAL,
            'sonata_admin_extension' => array(
                'enabled' => 'auto',
                'form_group' => 'form.group_seo',
            ),
            'alternate_locale' => array(
                'enabled'  => false,
                'provider_id' => null,
            ),
            'content_listener' => array(
                'enabled'     => true,
                'content_key' => 'contentDocument'
            ),
        );

        $sources = array_map(function ($path) {
            return __DIR__.'/../../Resources/Fixtures/'.$path;
        }, array(
            'config/config_sitemap.xml',
        ));

        $this->assertProcessedConfigurationEquals($expectedConfiguration, $sources);
    }
}
