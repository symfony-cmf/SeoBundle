<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\Sitemap;

use Symfony\Cmf\Bundle\SeoBundle\Model\UrlInformation;

/**
 * Interface for Guesser, that set values on url information.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
interface GuesserInterface
{
    /**
     * Updates UrlInformation with new values.
     *
     * @param UrlInformation $urlInformation
     * @param object         $object
     * @param string         $sitemap
     */
    public function guessValues(UrlInformation $urlInformation, $object, $sitemap = 'default');
}
