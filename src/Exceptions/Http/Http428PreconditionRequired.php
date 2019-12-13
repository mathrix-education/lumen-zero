<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/428 Precondition Required
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/428
 * @codeCoverageIgnore
 */
abstract class Http428PreconditionRequired extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 428;
}
