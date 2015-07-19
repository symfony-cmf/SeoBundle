<?php

namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Unit\Sitemap;

use Symfony\Cmf\Bundle\SeoBundle\Model\UrlInformation;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\AbstractChain;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\GuesserInterface;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\LocationGuesser;

class LocationGuesserTest extends GuesserTestCase
{
    public function testGuessCreate()
    {
        $urlInformation = parent::testGuessCreate();
        $this->assertEquals('http://symfony.com', $urlInformation->getLocation());
    }

    /**
     * @inheritdoc
     */
    protected function createGuesser()
    {
        $urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $urlGenerator
            ->expects($this->any())
            ->method('generate')
            ->with($this, array(), true)
            ->will($this->returnValue('http://symfony.com'))
        ;

        return new LocationGuesser($urlGenerator);
    }

    /**
     * @inheritdoc
     */
    protected function createData()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getFields()
    {
        return array('Location');
    }
}
