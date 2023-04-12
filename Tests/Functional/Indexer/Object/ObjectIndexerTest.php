<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Tests\Functional\Indexer\Object;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Domain\Model\Client;
use Flowpack\OpenSearch\Domain\Model\Document;
use Flowpack\OpenSearch\Indexer\Object\ObjectIndexer;
use Flowpack\OpenSearch\Tests\Functional\Fixtures\Tweet;
use Flowpack\OpenSearch\Tests\Functional\Fixtures\TweetRepository;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Flow\Utility\Algorithms;

class ObjectIndexerTest extends FunctionalTestCase
{
    /**
     * @var bool
     */
    protected static $testablePersistenceEnabled = true;

    /**
     * @var TweetRepository
     */
    protected $testEntityRepository;

    /**
     * @var Client
     */
    protected $testClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->testEntityRepository = new TweetRepository();
        $this->testClient = $this->objectManager->get(ObjectIndexer::class)->getClient();
    }

    /**
     * @test
     */
    public function persistingNewObjectTriggersIndexing()
    {
        $testEntity = $this->createAndPersistTestEntity();
        $documentId = $this->persistenceManager->getIdentifierByObject($testEntity);

        $resultDocument = $this->testClient
            ->findIndex('flow_opensearch_functionaltests_twitter')
            ->findType('tweet')
            ->findDocumentById($documentId);
        $resultData = $resultDocument->getData();

        self::assertEquals($testEntity->getMessage(), $resultData['message']);
        self::assertEquals($testEntity->getUsername(), $resultData['username']);
    }

    /**
     * @test
     */
    public function updatingExistingObjectTriggersReindexing()
    {
        $testEntity = $this->createAndPersistTestEntity();
        $identifier = $this->persistenceManager->getIdentifierByObject($testEntity);

        $initialVersion = $this->testClient
            ->findIndex('flow_opensearch_functionaltests_twitter')
            ->findType('tweet')
            ->findDocumentById($identifier)
            ->getVersion();
        self::assertIsInt($initialVersion);

        $persistedTestEntity = $this->testEntityRepository->findByIdentifier($identifier);
        $persistedTestEntity->setMessage('changed message.');
        $this->testEntityRepository->update($persistedTestEntity);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $changedDocument = $this->testClient
            ->findIndex('flow_opensearch_functionaltests_twitter')
            ->findType('tweet')
            ->findDocumentById($identifier);

        // the version increments by two, since we index via AOP and via Doctrine lifecycle events
        // see https://github.com/Flowpack/Flowpack.OpenSearch/pull/36
        self::assertSame($initialVersion + 2, $changedDocument->getVersion());
        self::assertSame($changedDocument->getField('message'), 'changed message.');
    }

    /**
     * @test
     */
    public function removingObjectTriggersIndexRemoval()
    {
        $testEntity = $this->createAndPersistTestEntity();
        $identifier = $this->persistenceManager->getIdentifierByObject($testEntity);

        $initialDocument = $this->testClient
            ->findIndex('flow_opensearch_functionaltests_twitter')
            ->findType('tweet')
            ->findDocumentById($identifier);
        self::assertInstanceOf(Document::class, $initialDocument);

        $persistedTestEntity = $this->testEntityRepository->findByIdentifier($identifier);
        $this->testEntityRepository->remove($persistedTestEntity);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $foundDocument = $this->testClient
            ->findIndex('flow_opensearch_functionaltests_twitter')
            ->findType('tweet')
            ->findDocumentById($identifier);
        self::assertNull($foundDocument);
    }

    protected function createAndPersistTestEntity()
    {
        $testEntity = new Tweet();
        $testEntity->setDate(new \DateTime());
        $testEntity->setMessage('This is a test message ' . Algorithms::generateRandomString(8));
        $testEntity->setUsername('Zak McKracken' . Algorithms::generateRandomString(8));

        $this->testEntityRepository->add($testEntity);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();
        return $testEntity;
    }
}
