<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Unit\Sitemap;

use Symfony\Cmf\Bundle\SeoBundle\AlternateLocaleProviderInterface;
use Symfony\Cmf\Bundle\SeoBundle\Model\AlternateLocale;
use Symfony\Cmf\Bundle\SeoBundle\Model\AlternateLocaleCollection;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\AlternateLocalesGuesser;

class AlternateLocalesGuesserTest extends GuesserTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createGuesser()
    {
        $collection = new AlternateLocaleCollection();
        $collection->add(new AlternateLocale('http://symfony.com/fr', 'fr'));

        $localeProvider = $this->createMock(AlternateLocaleProviderInterface::class);
        $localeProvider
            ->expects($this->any())
            ->method('createForContent')
            ->with($this)
            ->will($this->returnValue($collection))
        ;

        return new AlternateLocalesGuesser($localeProvider);
    }

    /**
     * {@inheritdoc}
     */
    protected function createData()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFields()
    {
        return ['AlternateLocales'];
    }
}
