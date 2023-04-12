<?php

declare(strict_types=1);

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

use Flowpack\OpenSearch\Annotations as OpenSearch;
use Neos\Flow\Annotations as Flow;

/**
 * An object for the "twitter" index, representing a "tweet" document.
 *
 * @Flow\Entity
 * @OpenSearch\Indexable(indexName="flow_opensearch_functionaltests_twitter", typeName="tweet")
 */
class Tweet
{
    /**
     * @var string
     * @OpenSearch\Mapping(boost=2.0, term_vector="with_offsets")
     */
    protected $username;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \DateTime
     * @OpenSearch\Mapping(format="YYYY-MM-dd")
     * @OpenSearch\Transform("Date", options={ "format": "Y-m-d" })
     */
    protected $date;

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
