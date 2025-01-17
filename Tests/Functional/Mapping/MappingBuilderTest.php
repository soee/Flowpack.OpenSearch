<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Tests\Functional\Mapping;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Domain\Model\Mapping;
use Flowpack\OpenSearch\Mapping\EntityMappingBuilder;
use Neos\Flow\Tests\FunctionalTestCase;

class MappingBuilderTest extends FunctionalTestCase
{
    protected EntityMappingBuilder $mappingBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->mappingBuilder = $this->objectManager->get(EntityMappingBuilder::class);
    }

    /**
     * @test
     */
    public function basicTest()
    {
        $information = $this->mappingBuilder->buildMappingInformation();
        self::assertGreaterThanOrEqual(2, count($information));
        self::assertInstanceOf(Mapping::class, $information[0]);
    }
}
