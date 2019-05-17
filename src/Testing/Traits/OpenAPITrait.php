<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Nyholm\Psr7\Factory\Psr17Factory;
use Rebilly\OpenAPI\PhpUnit\Asserts as RebillyOpenAPIAsserts;
use Rebilly\OpenAPI\Schema;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

/**
 * Trait OpenAPITrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property-read Application $app
 * @mixin MakesHttpRequests
 */
trait OpenAPITrait
{
    // We sadly need to rename those methods to override them since we need a parser for
    use DispatcherTrait, RebillyOpenAPIAsserts {
        RebillyOpenAPIAsserts::assertRequestBody as RebillyAssertRequestBody;
        RebillyOpenAPIAsserts::assertResponseBody as RebillyAssertResponseBody;
    }

    /** @var string The OpenAPI Specification entry file. MUST BE JSON! */
    public static $SpecPath = "docs/spec.json";
    /** @var Schema The OpenAPI Schema. */
    protected static $schema;
    /** @var string The request method. */
    protected $requestMethod;
    /** @var string The request URI. */
    protected $requestUri;


    /**
     * Assert that the current response follow the given OpenAPI specification.
     * We need the request URI since the specification does not use the
     *
     * @param string $openAPIUri The OpenAPI uri. If null, we will try to auto-detect it.
     */
    protected function assertOpenAPIResponse(?string $openAPIUri = null): void
    {
        // Convert Illuminate HTTP Response to PSR-17 Response
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrResponse = $psrHttpFactory->createResponse($this->response);

        if ($openAPIUri === null) {
            $openAPIUri = $this->getOpenAPIUri($this->requestMethod, $this->requestUri);
        }

        // Get the request uri
        self::assertResponse(self::$schema, $openAPIUri, $this->requestMethod, $psrResponse);
    }


    /**
     * Get the OpenAPI URI based on the actual request URI.
     *
     * @param string $method The HTTP method (GET, POST, PUT, PATCH, DELETE etc.)
     * @param string $actualUri The actual request URI
     *
     * @return string
     */
    public function getOpenAPIUri(string $method, $actualUri)
    {
        $currentRouter = $this->dispatch($method, $actualUri);

        $filteredRoutes = Collection::make($this->app->router->getRoutes())
            // Reject routes which do not match the method
            ->reject(function ($routeData, $routeKey) use ($method) {
                return !Str::startsWith($routeKey, strtoupper($method));
            })
            // Reject routes which do not match the controller/action
            ->reject(function ($routeData) use ($currentRouter) {
                if (!isset($routeData["action"]["uses"])) {
                    return true;
                }

                return $currentRouter[1]["uses"] !== $routeData["action"]["uses"];
            })
            // Reject routes which does not have the same arguments
            ->reject(function ($routeData, $routeKey) use ($currentRouter) {
                $pattern = "/{([a-zA-Z]+):[a-zA-Z0-9\/\\\+\-_\[\]\*\{\}\|\.\^]+}/";
                $strippedUri = preg_replace($pattern, '{$1}', $routeKey);

                $paramKeys = Collection::make(array_keys($currentRouter[2]))->map(function ($param) {
                    return "{{$param}}";
                });

                return !Str::contains($strippedUri, $paramKeys->toArray());
            });

        if ($filteredRoutes->isEmpty()) {
            return $actualUri;
        } else {
            $match = $filteredRoutes->first();

            return $match["uri"];
        }
    }
}
