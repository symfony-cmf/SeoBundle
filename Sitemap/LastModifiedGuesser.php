<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\Sitemap;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Symfony\Cmf\Bundle\SeoBundle\Model\UrlInformation;

/**
 * This guesser will add last modified date of an document to the url information, that can be rendered
 * to the sitemap.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LastModifiedGuesser implements GuesserInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * LastModifiedGuesser constructor.
     *
     * @param ManagerRegistry $manager
     */
    public function __construct(ManagerRegistry $manager)
    {
        $this->managerRegistry = $manager;
    }

    /**
     * Updates UrlInformation with new values if they are not already set.
     *
     * @param UrlInformation $urlInformation The value object to update.
     * @param object $object The sitemap element to get values from.
     * @param string $sitemap Name of the sitemap being built.
     * @return null
     */
    public function guessValues(UrlInformation $urlInformation, $object, $sitemap)
    {
        if (null !== $urlInformation->getLastModification()) {
            return null;
        }
        
        $className = ClassUtils::getRealClass(get_class($object));
        $manager = $this->managerRegistry->getManagerForClass($className);
        if (!$manager instanceof DocumentManager) {
            return null;
        }

        /** @var ClassMetadata $metadata */
        $metadata = $manager->getClassMetadata($className);
        $mixins = $metadata->getMixins();

        if (!in_array('mix:lastModified', $mixins)) {
            return null;
        }

        $fields = array_filter($metadata->getFieldNames(), function ($fieldName) use ($metadata) {
            $field = $metadata->getField($fieldName);

            return 'jcr:lastModified' == $field['property'];
        });

        if (1 !== count($fields)) {
            return null;
        }
        $fieldName = array_shift($fields);

        $urlInformation->setLastModification($metadata->getFieldValue($object, $fieldName));
    }
}
