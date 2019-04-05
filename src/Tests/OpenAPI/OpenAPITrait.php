<?php

namespace Mathrix\Lumen\Tests\OpenAPI;

use Illuminate\Support\Str;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Lumen\Tests\Traits\DispatcherTrait;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\StreamInterface;
use Rebilly\OpenAPI\PhpUnit\Asserts as RebillyOpenAPIAsserts;
use Rebilly\OpenAPI\PhpUnit\JsonSchemaConstraint;
use Rebilly\OpenAPI\Schema;
use stdClass;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

/**
 * Trait OpenAPITrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property-read Application $app
 * @mixin DispatcherTrait
 * @mixin MakesHttpRequests
 */
trait OpenAPITrait
{
    // We sadly need to rename those methods to override them since we need a parser for
    use RebillyOpenAPIAsserts {
        RebillyOpenAPIAsserts::assertRequestBody as RebillyAssertRequestBody;
        RebillyOpenAPIAsserts::assertResponseBody as RebillyAssertResponseBody;
    }

    /** @var string The OpenAPI Specification entry file. MUST BE JSON! */
    public static $SpecPath = "docs/spec.json";
    /** @var Schema The OpenAPI Schema. */
    protected $schema;
    /** @var string The request method. */
    protected $requestMethod;
    /** @var string The request URI. */
    protected $requestUri;


    /**
     * Boot the OpenAPI trait.
     */
    protected function bootOpenAPI(): void
    {
        $this->schema = new Schema(base_path(OpenAPITrait::$SpecPath));
    }


    /**
     * Overridden method in order to save method and URI.
     *
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $this->requestMethod = $method;
        $this->requestUri = "/" . trim($uri, "/");
        parent::json($method, $uri, $data, $headers);
    }


    /**
     * Get the OpenAPI URI based on the actual request URI.
     * @param string $method The HTTP method (GET, POST, PUT, PATCH, DELETE etc.)
     * @param string $actualUri The actual request URI
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
            if (Str::start($route, $methodUpper) && $routeData["action"]["uses"] === $uses) {
                $uri = $routeData["uri"];
            }
        }

        // If we did not found the URI, return null.
        if ($uri === null) {
            return null;
        }

        // We now need to remove the Regex from the uri
        $pattern = '/{([a-zA-Z]+):[a-zA-Z0-9' . preg_quote("\\+-[]*{}|_/.") . ']+}/';
        $result = preg_replace($pattern, '{$1}', $uri);

        return $result;
    }


    /**
     * Assert that the current response follow the given OpenAPI specification.
     * We need the request URI since the specification does not use the
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
        self::assertResponse($this->schema, $openAPIUri, $this->requestMethod, $psrResponse);
    }


    /**
     * Overridden assertion in order to support the nullable property.
     *
     * @param Schema $schema
     * @param string $path
     * @param string $method
     * @param StreamInterface|null $body
     * @param string $msg
     *
     * @see RebillyOpenAPIAsserts::assertResponseBody
     */
    protected static function assertRequestBody(Schema $schema, string $path, string $method,
                                                StreamInterface $body = null, string $msg = ""): void
    {
        $bodySchema = self::preProcessSchema($schema->getRequestBodySchema($path, strtolower($method)));

        if ($bodySchema) {
            Assert::assertThat(
                json_decode($body),
                new JsonSchemaConstraint($bodySchema, "request body"),
                $msg
            );
        } else {
            Assert::assertEmpty(json_decode($body), $msg);
        }
    }


    /**
     * Transform schema to match Json Schema specification.
     *
     * @param stdClass|null $schema The schema to pre-process.
     *
     * @return stdClass|null The processed schema.
     */
    protected static function preProcessSchema(?stdClass $schema): ?stdClass
    {
        return (new NullablePreProcessor())->transform($schema);
    }


    /**
     * Overridden assertion in order to support the nullable property.
     *
     * @param Schema $schema
     * @param string $path
     * @param string $method
     * @param string $status
     * @param StreamInterface|null $body
     * @param string $msg
     */
    protected static function assertResponseBody(Schema $schema, string $path, string $method, string $status,
                                                 StreamInterface $body = null, string $msg = ""): void
    {
        $bodySchema = self::preProcessSchema($schema->getResponseBodySchema($path, strtolower($method), $status));

        if ($bodySchema) {
            Assert::assertThat(
                json_decode($body),
                new JsonSchemaConstraint($bodySchema, "response body"),
                $msg
            );
        } else {
            Assert::assertEmpty(json_decode($body), $msg);
        }
    }
}
