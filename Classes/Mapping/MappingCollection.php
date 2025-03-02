<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Mapping;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Flowpack\OpenSearch\Domain\Model\Client as OpenSearchClient;
use Flowpack\OpenSearch\Domain\Model\Mapping;

class MappingCollection extends ArrayCollection
{
    public const TYPE_BACKEND = 'backend';
    public const TYPE_ENTITY = 'entity';

    /**
     * One of self::TYPE_* constants
     *
     * @var string
     */
    protected $type;

    /**
     * @var OpenSearchClient
     */
    protected $client;

    /**
     * @param string $type
     */
    public function __construct($type = null)
    {
        parent::__construct([]);
        $this->type = $type;
    }

    /**
     * Returns a new collection of mappings of this collection that are not member of the $complementCollection.
     *
     * @param MappingCollection $complementCollection
     * @return MappingCollection
     */
    public function diffAgainstCollection(MappingCollection $complementCollection)
    {
        $returnMappings = new MappingCollection();
        foreach ($this as $entityMapping) {
            /** @var $entityMapping Mapping */
            $mapping = new Mapping(clone $entityMapping->getType());
            $saveMapping = false;
            foreach ($entityMapping->getProperties() as $propertyName => $propertySettings) {
                foreach ($propertySettings as $entitySettingKey => $entitySettingValue) {
                    $backendSettingValue = $complementCollection->getMappingSetting($entityMapping, $propertyName, $entitySettingKey);
                    if ($entitySettingValue !== $backendSettingValue) {
                        $mapping->setPropertyByPath([$propertyName, $entitySettingKey], $entitySettingValue);
                        $saveMapping = true;
                    }
                }
            }
            if ($saveMapping) {
                $returnMappings->add($mapping);
            }
        }

        return $returnMappings;
    }

    /**
     * Tells whether a member of this collection has a specific index/type/property settings value
     *
     * @param Mapping $inquirerMapping
     * @param string $propertyName
     * @param string $settingKey
     * @return mixed
     */
    public function getMappingSetting(Mapping $inquirerMapping, $propertyName, $settingKey)
    {
        foreach ($this as $memberMapping) {
            /** @var $memberMapping Mapping */
            if ($inquirerMapping->getType()->getName() === $memberMapping->getType()->getName()
                && $inquirerMapping->getType()->getIndex()->getName() === $memberMapping->getType()->getIndex()->getName()
            ) {
                return $memberMapping->getPropertyByPath([$propertyName, $settingKey]);
            }
        }

        return null;
    }

    /**
     * @return OpenSearchClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param OpenSearchClient $client
     */
    public function setClient(OpenSearchClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
