<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/412 Precondition Failed
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/412
 * @codeCoverageIgnore
 */
abstract class Http412PreconditionFailed extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 412;
}
