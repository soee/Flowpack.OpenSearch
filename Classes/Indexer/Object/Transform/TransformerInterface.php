<?php
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

/**
 */
interface TransformerInterface
{
    /**
     * Returns the OpenSearch type this transform() method returns
     *
     * @abstract
     * @return string
     */
    public function getTargetMappingType();

    /**
     * @param mixed $source
     * @param TransformAnnotation $annotation
     * @return mixed
     */
    public function transformByAnnotation($source, TransformAnnotation $annotation);
}
