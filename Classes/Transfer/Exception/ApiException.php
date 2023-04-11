<?php
declare(strict_types=1);

namespace Flowpack\OpenSearch\Transfer\Exception;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Transfer\Exception as OpenSearchException;

/**
 * This exception type is intended to map any error output that was returned by OpenSearch itself
 * If, for example, OpenSearch returns {"error":"IndexMissingException[[foo_bar] missing]","status":404}
 * this exception is raised.
 */
class ApiException extends OpenSearchException
{
}
