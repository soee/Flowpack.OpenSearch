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

use Flowpack\OpenSearch\Domain\Model;
use Flowpack\OpenSearch\Exception as OpenSearchException;
use Flowpack\OpenSearch\Indexer\Object\IndexInformer;
use Neos\Flow\Annotations as Flow;

/**
 * This collects mappings from a live instance
 *
 * @Flow\Scope("singleton")
 */
class BackendMappingBuilder
{
    /**
     * @var Model\Client
     */
    protected $client;

    /**
     * Gets populated to an array during buildMappingInformation(). Contains "empty" mappings that have no type
     * attached, in order to display these. If this stays NULL, the buildMappingInformation() has not yet been run.
     *
     * @var array|null
     */
    protected $indicesWithoutTypeInformation;

    /**
     * @Flow\Inject
     * @var IndexInformer
     */
    protected $indexInformer;

    /**
     * Builds a Mapping collection from the annotation sources that are present
     *
     * @return MappingCollection<Model\Mapping>
     * @throws OpenSearchException
     * @throws \Neos\Flow\Http\Exception
     */
    public function buildMappingInformation(): MappingCollection
    {
        if (!$this->client instanceof Model\Client) {
            throw new OpenSearchException('No client was given for mapping retrieval. Set a client BackendMappingBuilder->setClient().', 1339678111);
        }

        $this->indicesWithoutTypeInformation = [];

        $response = $this->client->request('GET', '/_mapping');
        $mappingInformation = new MappingCollection(MappingCollection::TYPE_BACKEND);
        $mappingInformation->setClient($this->client);
        $indexNames = $this->indexInformer->getAllIndexNames();

        foreach ($response->getTreatedContent() as $indexName => $indexSettings) {
            if (!in_array($indexName, $indexNames)) {
                continue;
            }
            $index = new Model\Index($indexName);
            if (empty($indexSettings)) {
                $this->indicesWithoutTypeInformation[] = $indexName;
            }
            foreach ($indexSettings as $typeName => $typeSettings) {
                $type = new Model\GenericType($index, $typeName);
                $mapping = new Model\Mapping($type);
                if (isset($typeSettings['properties'])) {
                    foreach ($typeSettings['properties'] as $propertyName => $propertySettings) {
                        foreach ($propertySettings as $key => $value) {
                            $mapping->setPropertyByPath([$propertyName, $key], $value);
                        }
                    }
                }
                $mappingInformation->add($mapping);
            }
        }

        return $mappingInformation;
    }

    /**
     * @param Model\Client $client
     */
    public function setClient(Model\Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return array
     * @throws OpenSearchException
     */
    public function getIndicesWithoutTypeInformation(): array
    {
        if ($this->indicesWithoutTypeInformation === null) {
            throw new OpenSearchException('For getting the indices having no mapping information attached, BackendMappingBuilder->buildMappingInformation() has to be run first.', 1339751812);
        }

        return $this->indicesWithoutTypeInformation;
    }
}
