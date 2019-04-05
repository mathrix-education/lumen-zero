<?php

namespace Mathrix\Tests\OpenAPI;

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Lumen\Services\OpenAPI\NullablePreProcessor;
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
     * Boot the OpenAPI trait.
     */
    protected function bootOpenAPI(): void
    {
        $this->schema = new Schema(base_path(OpenAPITrait::$SpecPath));
    }


    /**
     * Assert that the current response follow the given OpenAPI specification.
     */
    protected function assertOpenAPIResponse(): void
    {
        // Convert Illuminate HTTP Response to PSR-17 Response
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrResponse = $psrHttpFactory->createResponse($this->response);

        $this->assertResponse($this->schema, $this->requestUri, $this->requestMethod, $psrResponse);
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
    protected function assertRequestBody(Schema $schema, string $path, string $method, StreamInterface $body = null,
                                         string $msg = ""): void
    {
        $bodySchema = $this->preProcessSchema($schema->getRequestBodySchema($path, strtolower($method)));

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
    private function preProcessSchema(?stdClass $schema): ?stdClass
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
    protected function assertResponseBody(Schema $schema, string $path, string $method, string $status,
                                          StreamInterface $body = null, string $msg = ""): void
    {
        $bodySchema = $this->preProcessSchema($schema->getResponseBodySchema($path, strtolower($method), $status));

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
