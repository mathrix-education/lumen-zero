<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

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
     * Boot the OpenAPI trait.
     */
    protected static function bootOpenAPITrait(): void
    {
        self::$schema = new Schema(OpenAPITrait::$SpecPath);
    }


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
        $uses = $currentRouter[1]["uses"];
        $routes = $this->app->router->getRoutes();

        $methodUpper = strtoupper($method);
        $uri = null;

        foreach ($routes as $route => $routeData) {
            if (
                Str::startsWith($route, $methodUpper)
                && isset($routeData["action"]["uses"]) // Required for routes handled by a closure
                && $routeData["action"]["uses"] === $uses
            ) {
                $uri = $routeData["uri"];
                break;
            }
        }

        // If we did not found the URI, return null.
        if ($uri === null) {
            return null;
        }

        // We now need to remove the Regex from the uri
        $pattern = '/{([a-zA-Z]+):[a-zA-Z0-9\/' . preg_quote("\\+-_[]*{}|.^") . ']+}/';
        $result = preg_replace($pattern, '{$1}', $uri);

        return $result;
    }
}
