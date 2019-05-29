<?php

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
    use DatabaseTrait, EventHandlersTrait;

    /** @var BaseModel|string $modelClass */
    protected static $modelClass = null;

    /** @var array The test keys. */
    protected $testKeys = [];

    /** @var Request $request */
    protected $request;

    /** @var Factory $factory */
    protected $factory = null;
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
    public static function bootRESTTrait(): void
    {
        $class = get_called_class();
        static::$modelClass = ClassResolver::getModelClass(get_called_class());
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
        $args = [static::$modelClass];
        if (!empty($options["subFactory"])) {
            $args[] = $options["subFactory"];
        }

        /** @var Factory $factory */
        $factory = app()->make(Factory::class);

        return $factory->of(...$args);
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
    public function makeRESTJsonRequest(string $key, $before = null, $after = null, $options = []): void
    {
        [$method, $uri, $field, $relation] = RESTUtils::resolve(static::$modelClass, $key);
        $uri = "/" . ltrim($uri, "/"); // Be sure to prepend $uri by a slash

        if (in_array($method, ["get", "patch", "delete"])) {
            // Set the request model
            $this->requestModel = static::$modelClass::random($options["conditions"] ?? []);

            $placeholder = "{" . lcfirst(class_basename(static::$modelClass)) . Str::ucfirst($field) . "}";
            $identifier = $this->requestModel->{$field};

            $uri = str_replace($placeholder, $identifier, $uri);
        }

        if ($relation === null && in_array($method, ["post", "patch"])) {
            // Build the request body
            $this->beforeRequestData = $this->override(
                $this->getFactoryBuilder($options)->raw(),
                $before
            );
        } else if ($relation !== null && $method === "patch") {
            $ids = [1, 2, 3, 4]; // TODO: Automatically generate that
            $this->beforeRequestData = $this->override($ids, $before);
        }

        // Make the JSON request
        $this->event("before.json", $method, $uri);
        $this->json($method, $uri, $this->beforeRequestData ?? []);
        $this->request = $this->app["request"];
        $this->event("after.json", $method, $uri);

        // Make assertions
        $this->event("before.assertions");
        if ($relation === null && in_array($method, ["post", "patch"])) {
            // On creation/edition, assert that the model has been successfully saved.
            $this->afterRequestData = $this->override(
                $this->beforeRequestData,
                $after
            );

            $this->assertInDatabase(static::$modelClass::getTableName(), $this->afterRequestData);
        } else if ($relation === null && $method === "delete") {
            /*
             * On delete, assert that the model has been successfully deleted (its primary key does not exists in the
             * database anymore.
             */
            $this->assertNotInDatabase(static::$modelClass::getTableName(), [
                $this->requestModel->getKeyName() => $this->requestModel->getKey()
            ]);
        } else if ($relation !== null && $method === "patch") {
            // Assert that the pivot table has been updated
            $this->afterRequestData = $this->override(
                $this->beforeRequestData,
                $after
            );

            /** @var BelongsToMany $relation */
            $relation = with(new static::$modelClass)->{$relation}();
            $pivotTable = $relation->getTable();
            $parentKeyName = $relation->getForeignPivotKeyName();
            $relatedKeyName = $relation->getRelatedPivotKeyName();

            foreach ($this->afterRequestData as $relatedKey) {
                $this->assertInDatabase($pivotTable, [
                    $parentKeyName => $this->requestModel->getKey(),
                    $relatedKeyName => $relatedKey
                ]);
            }
        }

        $this->assertResponseOk();
        $this->event("after.assertions");
    }


    /**
     * Declare the test keys for "standard" REST tests.
     * @return array
     */
    public function restDataProvider(): array
    {
        $data = array_map(function (string $key) {
            return [$key];
        }, $this->testKeys);

        return array_combine($this->testKeys, $data);
    }


    /**
     * Test the "standard" REST using test keys.
     * @dataProvider restDataProvider
     */
    public function testREST(string $key): void
    {
        $this->makeRESTJsonRequest($key);
    }
}
