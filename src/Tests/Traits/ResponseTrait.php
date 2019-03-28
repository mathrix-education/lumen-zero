<?php

namespace Mathrix\Lumen\Tests\Traits;

use Helmich\JsonAssert\JsonAssertions;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Constraint\IsType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ResponseTrait.
 *
 * @author    Mathieu Bour <mathieu@mathrix.fr>
 * @author    Jérémie Levain <munezero999@live.fr>
 * @since     4.0.3
 * @copyright Mathrix Education SA
 * @package   Tests\Utils\Traits
 */
trait ResponseTrait
{
    use JsonAssertions;


    /**
     * Get a value of the JSON response, with the dot notation for the key.
     * @param string $key The key, dot-notated
     * @return mixed|null
     */
    public function getJsonResponseValue(string $key)
    {
        $responseData = (array)$this->getJsonResponseData();
        $responseDataFlatten = Arr::dot($responseData);

        if (isset($responseDataFlatten[$key])) {
            return $responseDataFlatten[$key];
        } else {
            return null;
        }
    }


    /**
     * Get the response data, assuming the response body is a valid JSON. If not, return null.
     * @return \stdClass|null
     */
    public function getJsonResponseData()
    {
        if ($this->response instanceof Response) {
            $response = $this->response->getContent();
            return json_decode($response);
        } else {
            return null;
        }
    }


    /**
     * @param int|null $page
     * @param int|null $perPage
     * @param int|null $total
     */
    public function assertIsPaginatedResponse(int $page = null, int $perPage = null, int $total = null)
    {
        $this->assertJsonResponseMatches([
            "page" => $page ?? new IsType("integer"),
            "per_page" => $perPage ?? new IsType("integer"),
            "total" => $total ?? new IsType("integer"),
        ]);
    }


    /**
     * Assert that the response is a valid JSON response and that it matches the given constraints.
     * @param array $constraints
     */
    public function assertJsonResponseMatches(array $constraints)
    {
        $data = $this->getJsonResponseData();
        if ($data !== null) {
            $this->assertJsonDocumentMatches((array)$data, $constraints);
        } else {
            $this->fail("The response is not a valid JSON.");
        }
    }


    /**
     * Assert that the response follows th standard JSON error format.
     * @param int $code The HTTP Status code.
     * @param string|null $error The error, underscore-cased.
     */
    public function assertJsonErrorResponse(int $code, string $error = null)
    {
        $this->assertResponseStatus($code);
        if ($error !== null) {
            $this->assertJsonResponseMatches([
                "error" => $error
            ]);
        }
    }
}
