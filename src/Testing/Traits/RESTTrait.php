<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use Mathrix\Lumen\Zero\Utils\RESTUtils;
use PHPUnit\Framework\Assert;

/**
 * Trait RESTTrait.
 * Helper for testing standard REST HTTP JSON requests.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin MakesHttpRequests
 * @mixin Assert
 */
trait RESTTrait
{
    use DatabaseTrait, EventHandlersTrait, JsonResponseTrait, OpenAPITrait;

    /** @var Factory $factory */
    protected $factory = null;
    /** @var BaseModel|string $modelClass */
    protected $modelClass = null;
    /** @var BaseModel The request model (used in get, patch and delete). */
    protected $requestModel = null;

    /** @var array exceptFactoryFields Field to ignore from the model factory. */
    protected $exceptFactoryFields = ["created_at", "updated_at"];
    /** @var array The data before the request is sent. */
    protected $beforeRequestData = [];
    /** @var array The data after the request has been sent. */
    protected $afterRequestData = [];


    /**
     * Initialize the REST Trait.
     */
    public function initializeREST(): void
    {
        $this->factory = app()->make(Factory::class);

        $this->handler("before.json", function (string $method, string $uri) {
            $this->requestMethod = $method;
            $this->requestUri = "/" . trim($uri, "/");
        });

        $this->modelClass = ClassResolver::getModelClass($this);
    }


    /**
     * Get the page and perPage parameters extract from options.
     *
     * @param array $options Options of the request (used: page, perPage).
     *
     * @return array
     */
    protected function getPaginationParameters(array $options): array
    {
        $page = $options["page"] ?? 0;
        $perPage = $options["perPage"] ?? 100;

        return [$page, $perPage];
    }


    /**
     * Get the Factory Builder for the current test Model class.
     *
     * @param array $options Options of the request (used: subFactory).
     *
     * @return FactoryBuilder
     */
    protected function getFactoryBuilder(array $options): FactoryBuilder
    {
        // Build args
        $args = [$this->modelClass];
        if (!empty($options["subFactory"])) {
            $args[] = $options["subFactory"];
        }

        /** @var FactoryBuilder $factory */
        return call_user_func_array([$this->factory, "of"], $args);
    }


    /**
     * Override data with an array or a callback.
     *
     * @param array $data The data.
     * @param array|callable $override The array or the callback which will override the data.
     *
     * @return array
     */
    private function override(array $data, $override = null): array
    {
        if (is_array($override)) {
            return array_replace_recursive($data, $override);
        } else if (is_callable($override)) {
            return $override($data);
        } else if (!empty($this->exceptFactoryFields)) {
            return Arr::except($data, $this->exceptFactoryFields);
        } else {
            return $data;
        }
    }


    /**
     * Make a REST JSON request, determined by the given key.
     *
     * @param string $key The REST key.
     * @param null $before The before modifier.
     * @param null $after The after modifier.
     * @param array $options The request options.
     */
    public function makeRESTJsonRequest(string $key, $before = null, $after = null, $options = [])
    {
        [$method, $uri] = RESTUtils::resolve($this->modelClass, $key);
        $uri = "/" . ltrim($uri, "/"); // Be sure to prepend $uri by a slash

        if (in_array($method, ["get", "patch", "delete"])) {
            // Set the request model
            $this->requestModel = $this->modelClass::random($options["conditions"] ?? []);
        }

        if (in_array($method, ["post", "patch"])) {
            // Build the request body
            $this->beforeRequestData = $this->override(
                $this->getFactoryBuilder($options)->raw(),
                $before
            );
        }

        // Make the JSON request
        $this->event("before.json", $method, $uri);
        $this->json($method, $uri, $this->beforeRequestData ?? []);
        $this->event("after.json", $method, $uri);

        // Make assertions
        $this->event("before.assertions");
        if (in_array($method, ["post", "patch"])) {
            // On creation/edition, assert that the model has been successfully saved.
            $this->afterRequestData = $this->override(
                $this->beforeRequestData,
                $after
            );

            $this->assertInDatabase($this->modelClass::getTableName(), $this->afterRequestData);
        } else if ($method === "delete") {
            /*
             * On delete, assert that the model has been successfully deleted (its primary key does not exists in the
             * database anymore.
             */
            $this->assertNotInDatabase($this->modelClass::getTableName(), [
                $this->requestModel->getKeyName() => $this->requestModel->getKey()
            ]);
        }

        $this->assertResponseOk();
        $this->assertOpenAPIResponse();
        $this->event("after.assertions");
    }
}
