<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\SeoBundle\Model;
use Burgov\Bundle\KeyValueFormBundle\KeyValueContainer;

/**
 * This class is a container for the metadata.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class SeoMetadata implements SeoMetadataInterface
{
    /**
     * Id for the document.
     */
    private $id;

    /**
     * For translatable metadata.
     */
    private $locale;

    /**
     * This string contains the information where we will find the original content.
     * Depending on the setting for the cmf_seo.original_route_pattern, it
     * will do a redirect to this url or create a canonical link with this
     * value as the href attribute.
     *
     * @var string
     */
    private $originalUrl;

    /**
     * If this string is set, it will be inserted as a meta tag for the page description.
     *
     * @var  string
     */
    private $metaDescription;

    /**
     * This comma separated list will contain the keywords for the page's meta information.
     *
     * @var string
     */
    private $metaKeywords;

    /**
     * @var string
     */
    private $title;

    /**
     * To store meta tags for type property.
     *
     * @var array
     */
    private $extraProperties = array();

    /**
     * To store extra meta tags for type name.
     *
     * @var array
     */
    private $extraNames = array();

    /**
     * To store meta tags for type http-equiv.
     *
     * @var array
     */
    private $extraHttp = array();

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritDoc}
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function setExtraProperties($extraProperties)
    {
        $this->extraProperties = $this->toArray($extraProperties);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtraProperties()
    {
        return $this->extraProperties;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addExtraProperty($key, $value)
    {
        $this->extraProperties[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setExtraNames($extraNames)
    {
        $this->extraNames = $this->toArray($extraNames);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtraNames()
    {
        return $this->extraNames;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addExtraName($key, $value)
    {
        $this->extraNames[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setExtraHttp($extraHttp)
    {
        $this->extraHttp = $this->toArray($extraHttp);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtraHttp()
    {
        return $this->extraHttp;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addExtraHttp($key, $value)
    {
        $this->extraHttp[$key] = $value;
    }

    private function toArray($data)
    {
        if ($data instanceof KeyValueContainer) {
            $data = $data->toArray();
        }

        if (!is_array($data)) {
                throw new \InvalidArgumentException('expected array');
        }

        return $data;
    }
}
