<?php

namespace Mathrix\Lumen\Zero\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class SuccessJsonResponse.
 * A successful json response with no data.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class SuccessJsonResponse extends JsonResponse
{
    /**
     * SuccessJsonResponse constructor.
     *
     * @param null $data The data.
     * @param array $meta The meta data.
     * @param int $status The HTTP status code (default 200).
     * @param array $headers The custom HTTP headers.
     * @param int $options The json_encode function options.
     */
    public function __construct($data = null, $meta = [], $status = 200, $headers = [], $options = 0)
    {
        if (is_string($data)) {
            // Handle string-only responses
            $meta = ["message" => $data];
        } else if (is_string($meta)) {
            // Handle string-only responses
            $meta = ["message" => $meta];
        }

        $meta["success"] = true;
        $meta["data"] = $data;

        parent::__construct($meta, $status, $headers, $options);
    }
}
