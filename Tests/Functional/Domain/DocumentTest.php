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

use Flowpack\OpenSearch\Domain\Model\Document;
use Flowpack\OpenSearch\Tests\Functional\Fixtures\TwitterType;

class DocumentTest extends AbstractTest
{
    /**
     * Array that returns sample data. Intentionally returns only one record.
     * @return array
     */
    public function simpleDocumentDataProvider()
    {
        return [
            [
                [
                    'user' => 'kimchy',
                    'post_date' => '2009-11-15T14:12:12',
                    'message' => 'trying out OpenSearch'
                ]
            ]
        ];
    }

    /**
     * @dataProvider simpleDocumentDataProvider
     * @test
     */
    public function idOfFreshNewDocumentIsPopulatedAfterStoring(array $data = null)
    {
        $document = new Document(new TwitterType($this->testingIndex), $data);
        self::assertNull($document->getId());
        $document->store();
        self::assertMatchesRegularExpression('/\w+/', $document->getId());
    }

    /**
     * @dataProvider simpleDocumentDataProvider
     * @test
     */
    public function versionOfFreshNewDocumentIsCreatedAfterStoringAndIncreasedAfterSubsequentStoring(array $data = null)
    {
        $document = new Document(new TwitterType($this->testingIndex), $data);
        self::assertNull($document->getVersion());
        $document->store();
        $idAfterFirstStoring = $document->getId();
        self::assertSame(1, $document->getVersion());
        $document->store();
        self::assertSame(2, $document->getVersion());
        self::assertSame($idAfterFirstStoring, $document->getId());
    }

    /**
     * @dataProvider simpleDocumentDataProvider
     * @test
     */
    public function existingIdOfDocumentIsNotModifiedAfterStoring(array $data)
    {
        $id = '42-1010-42';
        $document = new Document(new TwitterType($this->testingIndex), $data, $id);
        $document->store();
        self::assertSame($id, $document->getId());
    }
}
