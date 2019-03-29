<?php

namespace Mathrix\Lumen\Tests\Traits;

use Faker\Generator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\TestCase;
use Mathrix\Lumen\Bases\BaseModel;
use Mathrix\Lumen\Middleware\ScopeMiddleware;
use Mathrix\Lumen\Utils\ClassResolver;
use function FastRoute\simpleDispatcher;

/**
 * Trait RESTTrait.
 * Helper for testing standard REST HTTP JSON requests.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @mixin TestCase
 *
 * @property Generator $faker
 */
trait RESTTrait
{
    use DatabaseTrait, ResponseTrait, PassportTrait;

    /** @var Dispatcher $dispatcher */
    private $dispatcher;

    /** @var Factory $factory */
    protected $factory = null;
    /** @var string The models namespace */
    protected $modelsNamespace = "App\\Models";
    /** @var $modelClass BaseModel */
    protected $modelClass = null;
    /** @var string The Model table */
    protected $table = null;
    /** @var string The Model base uri; by default its table name */
    protected $baseUri = null;
    /** @var int The quest model id (used in get, patch and delete) */
    protected $requestModelId = null;
    /** @var array The data before the request is sent. */
    protected $beforeRequestData = [];
    /** @var array The data after the request has been sent. */
    protected $afterRequestData = [];


    /**
     * Initialize the REST Trait.
     */
    public function initializeREST(): void
    {
        $this->factory = app("Illuminate\Database\Eloquent\Factory");
        $this->discover();
    }


    /**
     * Discover parameters using the class name.
     */
    public function discover(): void
    {
        $supposedModelClass = ClassResolver::getModelClassFrom("Test", get_class($this));

        if (class_exists($supposedModelClass)) {
            $this->modelClass = $supposedModelClass;
            $model = new $supposedModelClass();

            if ($model instanceof Model) {
                $this->table = $this->baseUri = $model->getTable();
            }
        }
    }


    /**
     * Get the Dispatcher
     * @return Dispatcher
     */
    protected function getDispatcher(): Dispatcher
    {
        if (!$this->dispatcher instanceof Dispatcher) {
            $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
                foreach (app()->router->getRoutes() as $route) {
                    $r->addRoute($route['method'], $route['uri'], $route['action']);
                }
            });
        }

        return $this->dispatcher;
    }


    /**
     * Dispatch an uri.
     *
     * @param string $method The method.
     * @param string $uri The uri.
     *
     * @return array
     */
    protected function dispatch(string $method, string $uri): array
    {
        $method = mb_strtoupper($method);

        return $this->getDispatcher()->dispatch($method, $uri);
    }


    /**
     * Get the Factory Builder for the current test Model class.
     *
     * @param array $options Options of the request (used: subFactory).
     *
     * @return FactoryBuilder
     */
    private function getFactoryBuilder(array $options): FactoryBuilder
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
     * Get the middleware scope value for the given uri. If there are multiple scopes (comma-separated), randomly choose
     * one.
     *
     * @param string $method The method.
     * @param string $uri The uri with all arguments.
     *
     * @return string|null
     * @see ScopeMiddleware
     */
    public function getAnyScopes(string $method, string $uri): ?string
    {
        $result = $this->dispatch($method, $uri);

        if ($result[0] === Dispatcher::FOUND && !empty($result[1]["middleware"])) {
            foreach ($result[1]["middleware"] as $middleware) {
                if (Str::startsWith($middleware, "scope:")) {
                    $scopes = str_replace("scope:", "", $middleware);
                    $scopes = explode(",", $scopes);

                    return $this->faker->randomElement($scopes);
                }
            }
        }

        return null;
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
     * Set the request model id.
     *
     * @param array $options Options of the request (used: conditions).
     */
    private function setRequestModelId(array $options): void
    {
        $conditions = $options["conditions"] ?? [];
        $this->requestModelId = $this->modelClass::random($conditions)->id;
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
        } else {
            return $data;
        }
    }


    /**
     * Generic index call, paginated.
     *
     * @param array $options Options of the request.
     */
    public function restIndex(array $options = []): void
    {
        [$page, $perPage] = $this->getPaginationParameters($options);
        $uri = "/{$this->baseUri}/$page/$perPage";

        if ($scope = $this->getAnyScopes("get", $uri)) {
            $this->mockScope([$scope]);
        }

        $this->json("get", "/{$this->baseUri}/$page/$perPage");
    }


    /**
     * Generic get call.
     *
     * @param array $options Options of the request.
     */
    public function restGet(array $options = []): void
    {
        $this->setRequestModelId($options);

        $uri = "/{$this->baseUri}/{$this->requestModelId}";

        if ($scope = $this->getAnyScopes("get", $uri)) {
            $this->mockScope([$scope]);
        }

        $this->json("get", $uri);
    }


    /**
     * Generic post call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function restPost($before = null, $after = null, array $options = []): void
    {
        $this->beforeRequestData = $this->override(
            $this->getFactoryBuilder($options)->raw(),
            $before
        );

        $uri = "/{$this->baseUri}";

        if ($scope = $this->getAnyScopes("post", $uri)) {
            $this->mockScope([$scope]);
        }

        $this->json("post", $uri, $this->beforeRequestData);

        $this->afterRequestData = $this->override(
            $this->beforeRequestData,
            $after
        );
    }


    /**
     * Generic patch call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function restPatch($before = null, $after = null, array $options = []): void
    {
        $this->setRequestModelId($options);

        $this->beforeRequestData = $this->override(
            $this->getFactoryBuilder($options)->raw(),
            $before
        );

        $uri = "/{$this->baseUri}/{$this->requestModelId}";

        if ($scope = $this->getAnyScopes("patch", $uri)) {
            $this->mockScope([$scope]);
        }

        $this->json("patch", $uri, $this->beforeRequestData);

        $this->afterRequestData = $this->override(
            $this->beforeRequestData,
            $after
        );
    }


    /**
     * Generic delete call.
     *
     * @param array $options Options of the request.
     */
    public function restDelete(array $options = []): void
    {
        $uri = "/{$this->baseUri}/{$this->requestModelId}";

        if ($scope = $this->getAnyScopes("patch", $uri)) {
            $this->mockScope([$scope]);
        }

        $this->setRequestModelId($options);
        $this->json("delete", $uri);
    }


    /**
     * Generic by call.
     *
     * @param BaseModel|string $modelClass The related model class
     * @param array $options Options of the request.
     */
    public function restBy(string $modelClass, array $options = []): void
    {
        $conditions = $options["conditions"] ?? [];
        $this->requestModelId = $modelClass::random($conditions)->id;
        $relatedModelUri = Str::singular($modelClass::getTableName());
        [$page, $perPage] = $this->getPaginationParameters($options);
        $uri = "/{$this->baseUri}/by-$relatedModelUri/{$this->requestModelId}/$page/$perPage";

        if ($scope = $this->getAnyScopes("get", $uri)) {
            $this->mockScope([$scope]);
        }

        $this->json("get", $uri);
    }


    /**
     * Assert success for generic index call, paginated.
     *
     * @param array $options Options of the request.
     */
    public function assertRestIndexSuccess(array $options = []): void
    {
        [$page, $perPage] = $this->getPaginationParameters($options);

        $this->restIndex($options);

        // Assertions
        $this->assertResponseOk();
        $this->assertIsPaginatedResponse($page, $perPage);
    }


    /**
     * Assert success for generic get call.
     *
     * @param array $options Options of the request.
     */
    public function assertRestGetSuccess(array $options = []): void
    {
        $this->restGet($options);

        // Assertions
        $this->assertResponseOk();
        $this->assertEquals($this->requestModelId, $this->getJsonResponseValue("id"));
    }


    /**
     * Assert success for generic post call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function assertRestPostSuccess($before = null, $after = null, array $options = []): void
    {
        $this->restPost($before, $after, $options);

        // Assertions
        $this->assertResponseOk();
        $this->assertInDatabase($this->table, $this->afterRequestData);
    }


    /**
     * Assert success for generic patch call.
     *
     * @param array|callable $before Array or closure to override data before it is sent.
     * @param array|callable $after Array or closure to override data after it is sent.
     * @param array $options Options of the request.
     */
    public function assertRestPatchSuccess($before = null, $after = null, array $options = []): void
    {
        $this->restPatch($before, $after, $options);

        // Assertions
        $this->assertResponseOk();
        $this->assertInDatabase($this->table, $this->afterRequestData);
    }


    /**
     * Assert success for delete call.
     *
     * @param array $options Options of the request.
     */
    public function assertRestDeleteSuccess(array $options = []): void
    {
        $this->restDelete($options);

        // Assertions
        $this->assertResponseOk();
        $this->assertNotInDatabase($this->table, ["id" => $this->requestModelId]);
    }


    /**
     * Assert success for generic by call.
     *
     * @param BaseModel|string $modelClass The related model class
     * @param array $options Options of the request.
     */
    public function assertRestBySuccess(string $modelClass, array $options = []): void
    {
        $this->restBy($modelClass, $options);

        $this->assertResponseOk();
        $this->assertIsPaginatedResponse();
    }

}
