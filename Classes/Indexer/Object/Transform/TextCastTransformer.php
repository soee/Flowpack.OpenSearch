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
 * @Flow\Scope("singleton")
 */
class TextCastTransformer implements TransformerInterface
{
    /**
     * Returns the OpenSearch type this transform() method returns
     *
     * @return string
     */
    public function getTargetMappingType(): string
    {
        return 'text';
    }

    /**
     * @param mixed $source
     * @param TransformAnnotation $annotation
     * @return string
     */
    public function transformByAnnotation($source, TransformAnnotation $annotation): string
    {
        return (string)$source;
    }
}
