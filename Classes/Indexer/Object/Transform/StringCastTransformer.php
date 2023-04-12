<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Indexer\Object\Transform;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Annotations\Transform as TransformAnnotation;
use Neos\Flow\Annotations as Flow;

/**
 * @deprecated Since OpenSearch deprecated the type "string" with version 5.0
 * @Flow\Scope("singleton")
 */
class StringCastTransformer implements TransformerInterface
{
    /**
     * Returns the OpenSearch type this transform() method returns
     *
     * @return string
     */
    public function getTargetMappingType()
    {
        return 'string';
    }

    /**
     * @param mixed $source
     * @param TransformAnnotation $annotation
     * @return string
     */
    public function transformByAnnotation($source, TransformAnnotation $annotation)
    {
        return (string)$source;
    }
}
