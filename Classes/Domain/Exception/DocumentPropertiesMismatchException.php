<?php
namespace Flowpack\OpenSearch\Domain\Exception;

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
use Neos\Error\Messages\Result;

/**
 * Signals a mismatch between the document properties
 */
class DocumentPropertiesMismatchException extends OpenSearchException
{
    /**
     * @var Result
     */
    protected Result $errorResult;

    /**
     * @param Result $result
     * @return void
     */
    public function setErrorResult(Result $result): void
    {
        $this->errorResult = $result;
    }
}
