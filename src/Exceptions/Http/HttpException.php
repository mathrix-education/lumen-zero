<?php

namespace Mathrix\Lumen\Exceptions\Http;

use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Class HttpException.
 * Define the HTTP Exceptions basics.
 *
 * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class HttpException extends \Exception
{
    /** The HTTP error standard name */
    protected const ERROR = null;
    /** THE HTTP error standard code */
    protected const CODE = null;
    /** @var string Exception message; has to be manually defined */
    protected $message;
    /** @var array Exception data; has to be manually defined */
    protected $data;


    /**
     * HttpException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     * @param array $data
     */
    public function __construct($data = null, $message = null, Throwable $previous = null)
    {
        parent::__construct($message ?? $this->message, self::CODE, $previous);
        $this->data = $data;
    }


    public function __toString()
    {
        $body = [
            "error" => snake_case($this::ERROR),
            "message" => !empty($this->message) ? $this->message : "No message given",
            "data" => $this->data ?? []
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
            "error" => snake_case($this::ERROR),
            "message" => !empty($this->message) ? $this->message : "No message given",
            "data" => $this->data ?? []
        ];

        if (!app()->environment("master")) {
            $exceptions = [
                [
                    "exception" => get_class($this),
                    "trace" => explode("\n", $this->getTraceAsString())
                ]
            ];

            $exceptionIterator = $this;

            while ($exceptionIterator->getPrevious() instanceof \Exception) {
                $previous = $this->getPrevious();
                $exceptions[] = [
                    "exception" => get_class($previous),
                    "trace" => explode("\n", $previous->getTraceAsString())
                ];
                $exceptionIterator = $previous;
            }

            $body["debug"] = $exceptions;
        }

        return new JsonResponse($body, $this::CODE);
    }
}
