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

use Flowpack\OpenSearch\Annotations\Indexable as IndexableAnnotation;
use Flowpack\OpenSearch\Annotations\Mapping as MappingAnnotation;
use Flowpack\OpenSearch\Annotations\Transform;
use Flowpack\OpenSearch\Domain\Model\GenericType;
use Flowpack\OpenSearch\Domain\Model\Index as OpenSearchIndex;
use Flowpack\OpenSearch\Domain\Model\Mapping;
use Flowpack\OpenSearch\Exception as OpenSearchException;
use Flowpack\OpenSearch\Indexer\Object\IndexInformer;
use Flowpack\OpenSearch\Indexer\Object\Transform\TransformerFactory;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Utility\Arrays;
use Neos\Utility\TypeHandling;

/**
 * Builds the mapping information across the objects
 * @Flow\Scope("singleton")
 */
class EntityMappingBuilder
{
    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @Flow\Inject
     * @var TransformerFactory
     */
    protected $transformerFactory;

    /**
     * @Flow\Inject
     * @var IndexInformer
     */
    protected $indexInformer;

    /**
     * Builds a Mapping collection from the annotation sources that are present
     *
     * @return MappingCollection<Mapping>
     * @throws OpenSearchException
     */
    public function buildMappingInformation()
    {
        $mappings = new MappingCollection(MappingCollection::TYPE_ENTITY);
        foreach ($this->indexInformer->getClassesAndAnnotations() as $className => $annotation) {
            $mappings->add($this->buildMappingFromClassAndAnnotation($className, $annotation));
        }

        return $mappings;
    }

    /**
     * @param string $className
     * @param IndexableAnnotation $annotation
     * @return Mapping
     * @throws OpenSearchException
     */
    protected function buildMappingFromClassAndAnnotation($className, IndexableAnnotation $annotation)
    {
        $index = new OpenSearchIndex($annotation->indexName);
        $type = new GenericType($index, $annotation->typeName);
        $mapping = new Mapping($type);
        foreach ($this->indexInformer->getClassProperties($className) as $propertyName) {
            $this->augmentMappingByProperty($mapping, $className, $propertyName);
        }

        return $mapping;
    }

    /**
     * @param Mapping $mapping
     * @param string $className
     * @param string $propertyName
     * @return void
     * @throws OpenSearchException
     */
    protected function augmentMappingByProperty(Mapping $mapping, string $className, string $propertyName): void
    {
        list($propertyType) = $this->reflectionService->getPropertyTagValues($className, $propertyName, 'var');
        if (($transformAnnotation = $this->reflectionService->getPropertyAnnotation($className, $propertyName, Transform::class)) !== null) {
            $mappingType = $this->transformerFactory->create($transformAnnotation->type)->getTargetMappingType();
        } elseif ($propertyType === 'string') {
            // string must be mapped to text as OpenSearch does not support the 'string' type for version >=5.0
            $mappingType = 'text';
        } elseif (TypeHandling::isSimpleType($propertyType)) {
            $mappingType = $propertyType;
        } elseif ($propertyType === '\DateTime') {
            $mappingType = 'date';
        } else {
            throw new OpenSearchException('Mapping is only supported for simple types and DateTime objects; "' . $propertyType . '" given but without a Transform directive.');
        }

        $mapping->setPropertyByPath($propertyName, ['type' => $mappingType]);

        $annotation = $this->reflectionService->getPropertyAnnotation($className, $propertyName, MappingAnnotation::class);

        if ($annotation instanceof MappingAnnotation) {
            $mapping->setPropertyByPath($propertyName, $this->processMappingAnnotation($annotation, $mapping->getPropertyByPath($propertyName)));
            if ($annotation->getFields()) {
                $multiFields = [];
                foreach ($annotation->getFields() as $multiFieldAnnotation) {
                    $multiFieldIndexName = trim($multiFieldAnnotation->index_name);
                    if ($multiFieldIndexName === '') {
                        throw new OpenSearchException('Multi field require an unique index name "' . $className . '::' . $propertyName . '".');
                    }
                    if (isset($multiFields[$multiFieldIndexName])) {
                        throw new OpenSearchException('Duplicate index name in the same multi field is not allowed "' . $className . '::' . $propertyName . '".');
                    }
                    if (!$multiFieldAnnotation->type) {
                        // Fallback to the parent's type if not specified on multi-field
                        $multiFieldAnnotation->type = $mappingType;
                    }
                    $multiFields[$multiFieldIndexName] = $this->processMappingAnnotation($multiFieldAnnotation);
                }
                $mapping->setPropertyByPath([$propertyName, 'fields'], $multiFields);
            }
        }
    }

    /**
     * @param MappingAnnotation $annotation
     * @param array $propertyMapping
     * @return array
     */
    protected function processMappingAnnotation(MappingAnnotation $annotation, array $propertyMapping = [])
    {
        foreach ($annotation->getPropertiesArray() as $mappingDirective => $directiveValue) {
            if ($directiveValue === null) {
                continue;
            }
            $propertyMapping = Arrays::setValueByPath($propertyMapping, $mappingDirective, $directiveValue);
        }

        return $propertyMapping;
    }
}
