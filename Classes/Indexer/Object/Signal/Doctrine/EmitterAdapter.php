<?php

declare(strict_types=1);

namespace Flowpack\OpenSearch\Indexer\Object\Signal\Doctrine;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\ORM\Event\LifecycleEventArgs;
use Flowpack\OpenSearch\Indexer\Object\Signal\EmitterAdapterInterface;
use Flowpack\OpenSearch\Indexer\Object\Signal\SignalEmitter;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EmitterAdapter implements EmitterAdapterInterface
{
    /**
     * @Flow\Inject
     * @var SignalEmitter
     */
    protected $signalEmitter;

    /**
     * @param LifecycleEventArgs $eventArguments
     */
    public function postUpdate(LifecycleEventArgs $eventArguments)
    {
        $this->signalEmitter->emitObjectUpdated($eventArguments->getEntity());
    }

    /**
     * @param LifecycleEventArgs $eventArguments
     */
    public function postPersist(LifecycleEventArgs $eventArguments)
    {
        $this->signalEmitter->emitObjectPersisted($eventArguments->getEntity());
    }

    /**
     * @param LifecycleEventArgs $eventArguments
     */
    public function postRemove(LifecycleEventArgs $eventArguments)
    {
        $this->signalEmitter->emitObjectRemoved($eventArguments->getEntity());
    }
}
