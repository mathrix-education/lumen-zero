<?php

namespace Mathrix\Lumen\Tests\REST;

use Closure;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Lumen\Bases\BaseModel;
use Mathrix\Lumen\Tests\OpenAPI\OpenAPITrait;
use Mathrix\Lumen\Tests\Traits\DatabaseTrait;
use Mathrix\Lumen\Tests\Traits\JsonResponseTrait;
use Mathrix\Lumen\Utils\ClassResolver;
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
 * @property Generator $faker
 */
trait RESTTrait
{
    use DatabaseTrait, JsonResponseTrait, OpenAPITrait,
        RESTIndexTrait, RESTGetTrait, RESTPostTrait, RESTPatchTrait, RESTDeleteTrait, RESTGetByTrait, RESTRelationTrait;

    /** @var Factory $factory */
    protected $factory = null;
    /** @var string The models namespace */
    protected $modelsNamespace = "App\\Models";
    /** @var string $modelName */
    protected $modelName = null;
    /** @var BaseModel|string $modelClass */
    protected $modelClass = null;
    /** @var string $table The Model table. */
    protected $table = null;
    /** @var string The Model base uri; by default its table name. */
    protected $baseUri = null;
    /** @var BaseModel The request model (used in get, patch and delete). */
    protected $requestModel = null;
    /** @var array exceptFactoryFields Field to ignore from the model factory. */
    protected $exceptFactoryFields = ["created_at", "updated_at"];
    /** @var array The data before the request is sent. */
    protected $beforeRequestData = [];
    /** @var array The data after the request has been sent. */
    protected $afterRequestData = [];
    /** @var bool $openApi If the application uses OpenAPI specification. */
    protected $openApi = true;
    /** @var array The event handlers. */
    protected $handlers = [];


    /**
     * Initialize the REST Trait.
     */
    public function initializeREST(): void
    {
        $this->factory = app(Factory::class);
        $this->discover();

        $this->handler("before.json", function (string $method, string $uri) {
            $this->requestMethod = $method;
            $this->requestUri = "/" . trim($uri, "/");
        });
    }


    /**
     * Make a json request.
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $this->event("before.json", $method, $uri);
        $result = parent::json($method, $uri, $data, $headers);
        $this->event("after.json", $method, $uri);

        return $result;
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
                $this->modelName = ClassResolver::baseClassName($this->modelClass);
            }
        }
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
    }/** @noinspection PhpUnusedPrivateMethodInspection */


    /**
     * Set the request model.
     *
     * @param array $options Options of the request (used: conditions).
     */
    private function setRequestModel(array $options): void
    {
        $conditions = $options["conditions"] ?? [];
        $this->requestModel = $this->modelClass::random($conditions);
    }/** @noinspection PhpUnusedPrivateMethodInspection */


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
     * Register an event handler.
     *
     * @param $name
     * @param Closure $callback
     */
    public function handler(string $name, Closure $callback)
    {
        if (empty($this->handlers[$name])) {
            $this->handlers[$name] = [];
        }

        $this->handlers[$name][] = $callback;
    }


    /**
     * Fire the a named event.
     *
     * @param string $name
     * @param mixed ...$payload
     */
    public function event(string $name, ...$payload)
    {
        if (!empty($this->handlers[$name])) {
            foreach ($this->handlers[$name] as $callback) {
                $callback(...$payload);
            }
        }
    }
}
