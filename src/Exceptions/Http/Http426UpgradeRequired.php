<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/426 Upgrade Required
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/426
 * @codeCoverageIgnore
 */
abstract class Http426UpgradeRequired extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 426;
}
