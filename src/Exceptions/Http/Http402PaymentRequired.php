<?php


namespace Mathrix\Lumen\Zero\Exceptions\Http;

/**
 * Send a HTTP/402 Payment Required
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/402
 * @codeCoverageIgnore
 */
abstract class Http402PaymentRequired extends Http
{
    /** THE HTTP error standard code */
    protected const CODE = 402;
}
