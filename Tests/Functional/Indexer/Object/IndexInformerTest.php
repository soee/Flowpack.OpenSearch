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

use Flowpack\OpenSearch\Annotations\Indexable as IndexableAnnotation;
use Flowpack\OpenSearch\Indexer\Object\IndexInformer;
use Flowpack\OpenSearch\Tests\Functional\Fixtures;
use Neos\Flow\Tests\FunctionalTestCase;

class IndexInformerTest extends FunctionalTestCase
{
    /**
     * @var IndexInformer
     */
    protected $informer;

    public function setUp(): void
    {
        parent::setUp();
        $this->informer = $this->objectManager->get(IndexInformer::class);
    }

    /**
     * @test
     */
    public function classAnnotationTest()
    {
        $actual = $this->informer->getClassAnnotation(Fixtures\JustFewPropertiesToIndex::class);
        self::assertInstanceOf(IndexableAnnotation::class, $actual);
        self::assertSame('dummyindex', $actual->indexName);
        self::assertSame('sampletype', $actual->typeName);
    }

    /**
     * @test
     */
    public function classWithOnlyOnePropertyAnnotatedHasOnlyThisPropertyToBeIndexed()
    {
        $actual = $this->informer->getClassProperties(Fixtures\JustFewPropertiesToIndex::class);
        self::assertCount(1, $actual);
    }

    /**
     * @test
     */
    public function classWithNoPropertyAnnotatedHasAllPropertiesToBeIndexed()
    {
        $actual = $this->informer->getClassProperties(Fixtures\Tweet::class);
        self::assertGreaterThan(1, $actual);
    }
}
