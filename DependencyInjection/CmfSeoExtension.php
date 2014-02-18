<?php

namespace Symfony\Cmf\Bundle\SeoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Routing\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class CmfSeoExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('admin.xml');

        $this->loadTitle($config['title'], $container);

        $this->loadContent($config['content'], $container);

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);
        }
    }

    /**
     * Fits the phpcr settings to its position.
     *
     * @param $config
     * @param XmlFileLoader    $loader
     * @param ContainerBuilder $container
     */
    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $container->setParameter($this->getAlias() . '.backend_type_phpcr', true);

        if ($config['use_sonata_admin']) {
            $this->loadSonataAdmin($config, $loader, $container);
        }
    }

    /**
     * Adds/loads the admin mapping if for the right values of the use_sonata_admin setting.
     *
     * @param $config
     * @param XmlFileLoader    $loader
     * @param ContainerBuilder $container
     */
    public function loadSonataAdmin($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if ('auto' === $config['use_sonata_admin'] && !isset($bundles['SonataDoctrinePHPCRAdminBundle'])) {
            return;
        }

        $loader->load('admin.xml');
    }

    /**
     * Just fits the title values into its position and creates a parameter array.
     *
     * @param $title
     * @param ContainerBuilder $container
     */
    private function loadTitle($title, ContainerBuilder $container)
    {
        $container->setParameter($this->getAlias().'.title', true);

        foreach ($title as $key => $value) {
            $container->setParameter($this->getAlias().'.title.'.$key, $value);
        }

        $container->setParameter($this->getAlias().'.title_parameters', $title);
    }

    /**
     * Fits all parameters under content into its position and creates a parameter array.
     *
     * @param $content
     * @param ContainerBuilder $container
     */
    private function loadContent($content, ContainerBuilder $container)
    {
        $container->setParameter($this->getAlias().'.content', true);

        foreach ($content as $key => $value) {
            $container->setParameter($this->getAlias().'.content.'.$key, $value);
        }
        $container->setParameter($this->getAlias().'.content_parameters', $content);
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/seo';
    }
}
