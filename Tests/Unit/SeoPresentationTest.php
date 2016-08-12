<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Unit;

use Symfony\Cmf\Bundle\SeoBundle\DependencyInjection\ConfigValues;
use Symfony\Cmf\Bundle\SeoBundle\SeoPresentation;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This test will cover the behavior of the SeoPresentation Model
 * This model is responsible for putting the SeoMetadata into
 * sonatas PageService.
 */
class SeoPresentationTest extends \PHPUnit_Framework_Testcase
{
    private $seoPresentation;
    private $pageService;
    private $seoMetadata;
    private $translator;
    private $content;
    private $configValues;
    private $loader;

    public function setUp()
    {
        $this->pageService = $this->getMock('Sonata\SeoBundle\Seo\SeoPage');
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->seoMetadata = $this->getMock('Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata');
        $this->content = new \stdClass();

        $this->loader = $this->getMock(LoaderInterface::class);
        $this->loader
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->seoMetadata));

        $this->configValues = new ConfigValues();
        $this->configValues->setDescription('default_description');
        $this->configValues->setTitle('default_title');
        $this->configValues->setOriginalUrlBehaviour(SeoPresentation::ORIGINAL_URL_CANONICAL);

        $this->seoPresentation = new SeoPresentation(
            $this->pageService,
            $this->translator,
            $this->configValues,
            $this->loader
        );
    }

    public function testDefaultTitle()
    {
        // promises
        $this->seoMetadata
            ->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue('Title test'))
        ;
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->with('default_title')
            ->will($this->returnValue('Title test | Default Title'))
        ;

        // predictions
        $this->pageService
            ->expects($this->once())
            ->method('setTitle')
            ->with('Title test | Default Title')
        ;

        // test
        $this->seoPresentation->updateSeoPage(new \stdClass());
    }

    public function testContentTitle()
    {
        // promises
        $this->seoMetadata
            ->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue('Content title'))
        ;
        $this->configValues->setTitle(null);

        // predictions
        $this->pageService
            ->expects($this->once())
            ->method('setTitle')
            ->with('Content title')
        ;

        // test
        $this->seoPresentation->updateSeoPage($this->content);
    }

    public function testDefaultDescription()
    {
        // promises
        $this->seoMetadata
            ->expects($this->any())
            ->method('getMetaDescription')
            ->will($this->returnValue('Test description.'))
        ;
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->with('default_description')
            ->will($this->returnValue('Default Description. Test description.'))
        ;

        // predictions
        $this->pageService
            ->expects($this->once())
            ->method('addMeta')
            ->with('name', 'description', 'Default Description. Test description.')
        ;

        // test
        $this->seoPresentation->updateSeoPage($this->content);
    }

    public function testContentDescription()
    {
        // promises
        $this->seoMetadata
            ->expects($this->any())
            ->method('getMetaDescription')
            ->will($this->returnValue('Content description.'))
        ;
        $this->configValues->setDescription(null);

        // predictions
        $this->pageService
            ->expects($this->once())
            ->method('addMeta')
            ->with('name', 'description', 'Content description.')
        ;

        // test
        $this->seoPresentation->updateSeoPage($this->content);
    }

    public function testSettingKeywordsToSeoPage()
    {
        // promises
        $this->seoMetadata
            ->expects($this->any())
            ->method('getMetaKeywords')
            ->will($this->returnValue('key1, key2'))
        ;
        $this->pageService
            ->expects($this->any())
            ->method('getMetas')
            ->will($this->returnValue(array(
                'name' => array(
                    'keywords' => array('default, other', array()),
                ),
            )))
        ;

        // predictions
        $this->pageService
            ->expects($this->once())
            ->method('addMeta')
            ->with('name', 'keywords', 'default, other, key1, key2')
        ;

        // test
        $this->seoPresentation->updateSeoPage($this->content);
    }

    public function testRedirect()
    {
        // promises
        $this->seoMetadata
            ->expects($this->any())
            ->method('getOriginalUrl')
            ->will($this->returnValue('/redirect/target'))
        ;
        $this->configValues->setOriginalUrlBehaviour(SeoPresentation::ORIGINAL_URL_REDIRECT);

        // test
        $this->seoPresentation->updateSeoPage($this->content);

        // assertions
        $redirect = $this->seoPresentation->getRedirectResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redirect);
        $this->assertEquals('/redirect/target', $redirect->getTargetUrl());
    }
}
