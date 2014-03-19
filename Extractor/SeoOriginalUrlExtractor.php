<?php

namespace Symfony\Cmf\Bundle\SeoBundle\Extractor;

use Symfony\Cmf\Bundle\SeoBundle\Exceptions\SeoExtractorStrategyException;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoAwareInterface;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadataInterface;

/**
 * Contrary to the SeoOriginalRouteExtractor this one will set a
 * absolute url as a string to the SeoMetadata.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@onit-gmbh.de>
 */
class SeoOriginalUrlExtractor implements SeoExtractorInterface
{

    /**
     * {@inheritDoc}
     */
    public function supports(SeoAwareInterface $document)
    {
        return $document instanceof SeoOriginalUrlInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function updateMetadata(SeoAwareInterface $document, SeoMetadataInterface $seoMetadata)
    {
        if (!$document instanceof SeoOriginalUrlInterface) {
            throw new SeoExtractorStrategyException(
                sprintf(
                    'The given document %s is not supported by this strategy. Call supports() method first.',
                    get_class($document)
                )
            );
        }
        $seoMetadata->setOriginalUrl($document->getSeoOriginalUrl());
    }
}
 