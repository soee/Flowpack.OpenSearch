<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Domain\Model;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Transfer\Response;
use Neos\Utility\Arrays;

/**
 * Reflects a Mapping of OpenSearch
 */
class Mapping
{
    public const NEOS_TYPE_FIELD = 'neos_type';

    /**
     * @var AbstractType
     */
    protected AbstractType $type;

    /**
     * @var array
     */
    protected array $properties = [];

    /**
     * see https://www.elastic.co/guide/en/elasticsearch/reference/current/dynamic-templates.html
     * @var array
     */
    protected array $dynamicTemplates = [];

    /**
     * This is the full / raw OpenSearch mapping which is merged with the properties and dynamicTemplates.
     *
     * It can be used to specify arbitrary OpenSearch mapping options, like f.e. configuring the _all field.
     *
     * @var array
     */
    protected array $fullMapping = [];

    /**
     * @param AbstractType $type
     */
    public function __construct(AbstractType $type)
    {
        $this->type = $type;
        $this->properties[static::NEOS_TYPE_FIELD] = ['type' => 'keyword'];
    }

    /**
     * Gets a property setting by its path
     *
     * @param array|string $path
     * @return mixed
     */
    public function getPropertyByPath($path)
    {
        return Arrays::getValueByPath($this->properties, $path);
    }

    /**
     * Gets a property setting by its path
     *
     * @param array|string $path
     * @param string $value
     */
    public function setPropertyByPath($path, $value): void
    {
        $this->properties = Arrays::setValueByPath($this->properties, $path, $value);
    }

    /**
     * @return AbstractType
     */
    public function getType(): AbstractType
    {
        return $this->type;
    }

    /**
     * Sets this mapping to the server
     *
     * @return Response
     * @throws \Flowpack\OpenSearch\Exception
     * @throws \Neos\Flow\Http\Exception
     */
    public function apply(): Response
    {
        $content = json_encode($this->asArray());

        return $this->type->request('PUT', '/_mapping', [], $content);
    }

    /**
     * Return the mapping which would be sent to the server as array
     *
     * @return array
     */
    public function asArray(): array
    {
        return Arrays::arrayMergeRecursiveOverrule([
            'dynamic_templates' => $this->getDynamicTemplates(),
            'properties' => $this->getProperties(),
        ], $this->fullMapping);
    }

    /**
     * @return array
     */
    public function getDynamicTemplates(): array
    {
        return $this->dynamicTemplates;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Dynamic templates allow to define mapping templates
     *
     * @param string $dynamicTemplateName
     * @param array $mappingConfiguration
     */
    public function addDynamicTemplate(string $dynamicTemplateName, array $mappingConfiguration): void
    {
        $this->dynamicTemplates[] = [
            $dynamicTemplateName => $mappingConfiguration,
        ];
    }

    /**
     * See {@link setFullMapping} for documentation
     *
     * @return array
     */
    public function getFullMapping(): array
    {
        return $this->fullMapping;
    }

    /**
     * This is the full / raw OpenSearch mapping which is merged with the properties and dynamicTemplates.
     *
     * It can be used to specify arbitrary OpenSearch mapping options, like f.e. configuring the _all field.
     *
     * @param array $fullMapping
     */
    public function setFullMapping(array $fullMapping): void
    {
        $this->fullMapping = $fullMapping;
    }
}
