<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Tests\Functional\Domain;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Domain\Factory\ClientFactory;
use Flowpack\OpenSearch\Domain\Model\Index;
use Neos\Flow\Tests\FunctionalTestCase;

abstract class AbstractTest extends FunctionalTestCase
{
    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @var Index
     */
    protected $testingIndex;

    /**
     * @var bool
     */
    protected $removeIndexOnTearDown = false;

    /**
     * final because else it could seriously damage the Index in the unlikely case there's already an index named flow_OpenSearch_FunctionalTests
     */
    final public function setUp(): void
    {
        parent::setUp();

        $this->clientFactory = $this->objectManager->get(ClientFactory::class);
        $client = $this->clientFactory->create('FunctionalTests');
        $this->testingIndex = $client->findIndex('flow_opensearch_functionaltests');

        if ($this->testingIndex->exists()) {
            throw new \Exception('The index "flow_opensearch_functionaltests" already existed, aborting.', 1338967487);
        }
        $this->testingIndex->create();
        $this->removeIndexOnTearDown = true;

        $this->additionalSetUp();
    }

    /**
     * may be implemented by inheritors because setUp() is final.
     */
    protected function additionalSetUp()
    {
    }

    /**
     * set to final because this is an important step which may not be overridden.
     */
    final public function tearDown(): void
    {
        parent::tearDown();

        if ($this->removeIndexOnTearDown === true) {
            $this->testingIndex->delete();
        }
    }
}
