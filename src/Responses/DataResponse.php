<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Responses;

use Illuminate\Http\JsonResponse;
use function is_string;

class DataResponse extends JsonResponse
{
    /**
     * @param null         $data    The data.
     * @param array|string $meta    The meta data.
     * @param int          $status  The HTTP status code (default 200).
     * @param array        $headers The custom HTTP headers.
     * @param int          $options The json_encode function options.
     */
    public function __construct($data = null, $meta = [], $status = 200, $headers = [], $options = 0)
    {
        if (is_string($data)) {
            // Handle string-only responses
            $meta = ['message' => $data];
        } elseif (is_string($meta)) {
            // Handle string-only responses
            $meta = ['message' => $meta];
        }

        $meta['data'] = $data;

        parent::__construct($meta, $status, $headers, $options);
    }
}
