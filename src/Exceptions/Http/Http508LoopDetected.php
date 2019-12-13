<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/508 Loop Detected
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/508
 * @codeCoverageIgnore
 */
abstract class Http508LoopDetected extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 508;
}
