<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/507 Insufficient Storage
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/507
 *
 * @codeCoverageIgnore
 */
abstract class Http507InsufficientStorage extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 507;
}
