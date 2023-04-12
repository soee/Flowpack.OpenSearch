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

use Flowpack\OpenSearch\Exception as OpenSearchException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

/**
 * @Flow\Scope("singleton")
 */
class TransformerFactory
{
    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param string $annotatedTransformer Either a full qualified class name or a shortened one which is seeked in the current package.
     * @return TransformerInterface
     * @throws OpenSearchException
     */
    public function create($annotatedTransformer)
    {
        if (!class_exists($annotatedTransformer)) {
            $annotatedTransformer = 'Flowpack\OpenSearch\Indexer\Object\Transform\\' . $annotatedTransformer . 'Transformer';
        }
        $transformer = $this->objectManager->get($annotatedTransformer);
        if (!$transformer instanceof TransformerInterface) {
            throw new OpenSearchException(sprintf('The transformer instance "%s" does not implement the TransformerInterface.', $annotatedTransformer), 1339598316);
        }

        return $transformer;
    }
}
