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

namespace Symfony\Cmf\Bundle\SeoBundle\Model;

/**
 * Value object for properties of an alternate locale.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class AlternateLocale
{
    public const REL = 'alternate';

    /**
     * @var string the absolute url for that locale
     */
    public $href;

    /**
     * @var string The locale/language in the following formats: de, de-DE
     */
    public $hrefLocale;

    public function __construct($href, $hrefLocale)
    {
        $this->href = $href;
        $this->hrefLocale = $hrefLocale;
    }
}
