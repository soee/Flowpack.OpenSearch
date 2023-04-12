<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Domain\Factory;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Domain\Exception\DocumentPropertiesMismatchException;
use Flowpack\OpenSearch\Domain\Model;
use Flowpack\OpenSearch\Domain\Model\Document;
use Flowpack\OpenSearch\Transfer\Response;
use Neos\Error\Messages\Error;
use Neos\Error\Messages\Result as ErrorResult;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;

/**
 * Reconstitute a Document from the OpenSearch index.
 *
 * TODO: maybe move into Document::createFromResponse()
 *
 * @Flow\Scope("singleton")
 */
class DocumentFactory
{
    /**
     * @param Model\AbstractType $type
     * @param string $id
     * @param Response $response
     * @return Document
     * @throws DocumentPropertiesMismatchException
     */
    public function createFromResponse(Model\AbstractType $type, string $id, Response $response): Document
    {
        $content = $response->getTreatedContent();

        $verificationResults = new ErrorResult();
        if (isset($content['_index']) && $type->getIndex()->getName() !== $content['_index']) {
            $error = new Error('The received index name "%s" does not match the expected one "%s".', 1340264838, [$content['_index'], $type->getIndex()->getName()]);
            $verificationResults->addError($error);
        }
        if ($type->getName() !== Arrays::getValueByPath($content, '_source.neos_type')) {
            $error = new Error('The received type name "%s" does not match the expected one "%s".', 1340265103, [$content['_type'], $type->getName()]);
            $verificationResults->addError($error);
        }

        if (isset($content['_id']) && $id !== null && $id !== $content['_id']) {
            $error = new Error('The received id "%s" does not match the expected one "%s".', 1340269758, [$content['_id'], $id]);
            $verificationResults->addError($error);
        }

        if ($verificationResults->hasErrors()) {
            $exception = new DocumentPropertiesMismatchException('The document\'s properties do not match the expected ones.', 1340265248);
            $exception->setErrorResult($verificationResults);
            throw $exception;
        }

        $version = $content['_version'];
        $data = $content['_source'];

        return new Document($type, $data, $id, $version);
    }
}
