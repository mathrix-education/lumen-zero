<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Testing\Traits;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Mathrix\Lumen\Zero\Exceptions\InvalidArgument;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Registrars\ZeroRouter;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use PHPUnit\Framework\Assert;
use function app;
use function array_replace_recursive;
use function in_array;
use function is_array;
use function is_callable;
use function str_replace;
use function with;

/**
 * Helper for testing standard CRUD HTTP JSON requests.
 *
 * @property Application $app
 * @mixin MakesHttpRequests
 * @mixin Assert
 */
trait CRUD
{
    use DatabaseAssertions;
    use HasEventHandlers;

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
    protected $exceptFactoryFields = ['created_at', 'updated_at'];
    /** @var array The data before the request is sent. */
    protected $beforeRequestData = [];
    /** @var array The data after the request has been sent. */
    protected $afterRequestData = [];

    /**
     * Initialize the REST Trait.
     *
     * @noinspection PhpUnused
     */
    public static function bootCRUD(): void
    {
        static::$modelClass = ClassResolver::getModelClass(static::class);
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
        if (!empty($options['subFactory'])) {
            $args[] = $options['subFactory'];
        }

        /** @var Factory $factory */
        $factory = app()->make(Factory::class);

        return $factory->of(...$args);
    }

    /**
     * Override data with an array or a callback.
     *
     * @param array          $data     The data.
     * @param array|callable $override The array or the callback which will override the data.
     *
     * @return array
     */
    private function override(array $data, $override = null): array
    {
        if (is_array($override)) {
            return array_replace_recursive($data, $override);
        }

        if (is_callable($override)) {
            return $override($data);
        }

        if (!empty($this->exceptFactoryFields)) {
            return Arr::except($data, $this->exceptFactoryFields);
        }

        return $data;
    }

    /**
     * Make a REST JSON request, determined by the given key.
     *
     * @param string $key     The REST key.
     * @param null   $before  The before modifier.
     * @param null   $after   The after modifier.
     * @param array  $options The request options.
     *
     * @throws InvalidArgument
     */
    public function makeRequest(string $key, $before = null, $after = null, $options = []): void
    {
        [$method, $uri] = ZeroRouter::resolve($key, static::$modelClass);
        /**
         * @var bool $isRelationRequest if the request is a relation request (which means that the key contains a ':'
         */
        $isRelationRequest = Str::contains($uri, ':');

        // Replace {identifier} by a real model identifier
        if (!$isRelationRequest) {
            // Set the request model
            $this->requestModel = static::$modelClass::random($options['conditions'] ?? []);

            $uri = str_replace("{{$this->requestModel->getSelector()}}", $this->requestModel->getKey(), $uri);
        }

        // Build the request body if necessary
        if (in_array($method, ['post', 'patch'])) {
            if (!$isRelationRequest) {
                $this->beforeRequestData = $this->override(
                    $this->getFactoryBuilder($options)->raw(),
                    $before
                );
            } else {
                $ids                     = [1, 2, 3, 4]; // TODO: Automatically generate that
                $this->beforeRequestData = $this->override($ids, $before);
            }
        }

        // Make the JSON request
        $this->event('before.json', $method, $uri);
        $this->json($method, $uri, $this->beforeRequestData ?? []);
        $this->request = $this->app['request'];
        $this->event('after.json', $method, $uri);

        // Make assertions
        $this->event('before.assertions');
        $this->debug();
        if (!$isRelationRequest && in_array($method, ['post', 'patch'])) {
            // On creation/edition, assert that the model has been successfully saved.
            $this->afterRequestData = $this->override(
                $this->beforeRequestData,
                $after
            );

            $this->assertInDatabase(static::$modelClass::getTableName(), $this->afterRequestData);
        } elseif (!$isRelationRequest && $method === 'delete') {
            /*
             * On delete, assert that the model has been successfully deleted (its primary key does not exists in the
             * database anymore).
             */
            $this->assertNotInDatabase(static::$modelClass::getTableName(), [
                $this->requestModel->getKeyName() => $this->requestModel->getKey(),
            ]);
        } elseif ($isRelationRequest && $method === 'patch') {
            // Assert that the pivot table has been updated
            $this->afterRequestData = $this->override(
                $this->beforeRequestData,
                $after
            );

            /** @var BelongsToMany $relation */
            $relation       = with(new static::$modelClass())->{$relation}();
            $pivotTable     = $relation->getTable();
            $parentKeyName  = $relation->getForeignPivotKeyName();
            $relatedKeyName = $relation->getRelatedPivotKeyName();

            foreach ($this->afterRequestData as $relatedKey) {
                $this->assertInDatabase($pivotTable, [
                    $parentKeyName  => $this->requestModel->getKey(),
                    $relatedKeyName => $relatedKey,
                ]);
            }
        }

        $this->assertResponseOk();
        $this->event('after.assertions');
    }
}
