<?php

namespace Flowpack\OpenSearch\Annotations;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\Common\Annotations\Annotation as DoctrineAnnotation;

/**
 * @Annotation
 * @DoctrineAnnotation\Target("PROPERTY")
 */
final class Transform
{
    /**
     * @var string
     */
    public string $type;

    /**
     * @var array
     */
    public array $options;
}
