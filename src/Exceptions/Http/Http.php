<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Exceptions\Http;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Throwable;
use const JSON_PRETTY_PRINT;
use function class_basename;
use function env;
use function explode;
use function get_class;
use function json_encode;
use function preg_replace;

/**
 * Define the HTTP Exceptions basics.
 *
 * @link https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
abstract class Http extends Exception
{
    /** THE HTTP error standard code */
    protected const CODE = null;
    /** @var string Exception error; will be displayed in the JSON response */
    protected $error;
    /** @var string Exception message; has to be manually defined */
    protected $message;
    /** @var array Exception data; has to be manually defined */
    protected $data;

    /**
     * @param string         $message
     * @param Throwable|null $previous
     * @param array          $data
     */
    public function __construct($data = null, $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? $this->message, self::CODE, $previous);

        $this->data = $data;

        if (!empty($this->error)) {
            return;
        }

        $this->error = Str::snake($this->getError());
    }

    /**
     * Get the exception error, extracted from the class name.
     * Examples:
     * - Http429TooManyRequestsException => TooManyRequests
     * - Http501NotImplementedException => NotImplemented
     * - ProductAlreadyBoughtException => ProductAlreadyBought
     *
     * @param string|null $name
     *
     * @return string
     */
    public function getError(?string $name = null): string
    {
        $name = $name ?: class_basename($this);

        return preg_replace('/(?:Http[0-9]{3})?([A-Za-z]+)Exception/', '$1', $name);
    }

    public function __toString()
    {
        $body = [
            'error' => $this->error,
            'message' => !empty($this->message) ? $this->message : 'No message given',
            'data' => $this->data ?? [],
        ];

        return json_encode($body, JSON_PRETTY_PRINT);
    }

    /**
     * Build JsonResponse object.
     *
     * @return JsonResponse
     */
    public function toJsonResponse(): JsonResponse
    {
        $body = [
            'error' => $this->error,
            'message' => !empty($this->message) ? $this->message : 'No message given',
            'data' => $this->data ?? [],
        ];

        if (!env('APP_DEBUG')) {
            $exceptions = [
                [
                    'exception' => static::class,
                    'trace' => explode("\n", $this->getTraceAsString()),
                ],
            ];

            $exceptionIterator = $this;

            while ($exceptionIterator->getPrevious() instanceof Exception) {
                $previous          = $this->getPrevious();
                $exceptions[]      = [
                    'exception' => get_class($previous),
                    'trace' => explode("\n", $previous->getTraceAsString()),
                ];
                $exceptionIterator = $previous;
            }

            $body['debug'] = $exceptions;
        }

        return new JsonResponse($body, $this::CODE);
    }
}
