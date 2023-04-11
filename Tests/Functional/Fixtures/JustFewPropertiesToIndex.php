<?php
namespace Flowpack\OpenSearch\Tests\Functional\Fixtures;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Flowpack\OpenSearch\Annotations as OpenSearch;

/**
 * This class contains just one property that has to be flagged as indexable.
 *
 * @Flow\Entity
 * @OpenSearch\Indexable(indexName="dummyindex", typeName="sampletype")
 */
class JustFewPropertiesToIndex
{
    /**
     * @var string
     * @OpenSearch\Indexable
     */
    protected $value1;

    /**
     * @var string
     */
    protected $value2;

    /**
     * @var string
     */
    protected $value3;
}
