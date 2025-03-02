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

use Flowpack\OpenSearch\Transfer\RequestService;
use Flowpack\OpenSearch\Transfer\Response;
use Neos\Flow\Annotations as Flow;

/**
 * A Client representation
 */
class Client
{
    /**
     * The cluster environment name.
     * With this key, configuration like the client configuration and the indices on that cluster are referenced.
     *
     * @var string
     */
    protected string $bundle = 'default';

    /**
     * @Flow\Inject
     * @var RequestService
     */
    protected RequestService $requestService;

    /**
     * @var array
     */
    protected array $clientConfigurations;

    /**
     * @var array
     */
    protected array $indexCollection = [];

    /**
     * @return string
     */
    public function getBundle(): string
    {
        return $this->bundle;
    }

    /**
     * @param string $bundle
     */
    public function setBundle(string $bundle): void
    {
        $this->bundle = $bundle;
    }

    /**
     * @return array
     */
    public function getClientConfigurations(): array
    {
        return $this->clientConfigurations;
    }

    /**
     * @param array $clientConfigurations
     */
    public function setClientConfigurations(array $clientConfigurations): void
    {
        $this->clientConfigurations = $clientConfigurations;
    }

    /**
     * @param string $indexName
     * @return Index
     * @throws \Flowpack\OpenSearch\Exception
     */
    public function findIndex(string $indexName): Index
    {
        if (!array_key_exists($indexName, $this->indexCollection)) {
            $this->indexCollection[$indexName] = new Index($indexName, $this);
        }

        return $this->indexCollection[$indexName];
    }

    /**
     * Passes a request through to the request service
     *
     * @param string $method
     * @param ?string $path
     * @param array $arguments
     * @param ?string|array $content
     * @return Response
     * @throws \Flowpack\OpenSearch\Transfer\Exception
     * @throws \Flowpack\OpenSearch\Transfer\Exception\ApiException
     * @throws \Neos\Flow\Http\Exception
     */
    public function request(string $method, ?string $path = null, array $arguments = [], string|array $content = null): Response
    {
        return $this->requestService->request($method, $this, $path, $arguments, $content);
    }
}
