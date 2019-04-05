<?php

namespace Mathrix\Lumen\Tests\Traits;

use Faker\Generator;
use FastRoute\Dispatcher;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Mathrix\Lumen\Utils\ClassResolver;

/**
 * Class PassportTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @property Generator $faker
 */
trait PassportTrait
{
    use DispatcherTrait;

    protected $passportUser;


    /**
     * Automatically mock Passport scopes based on the scope middleware route declaration.
     *
     * @param string $method The method.
     * @param string $uri The uri.
     */
    public function autoMockScope(string $method, string $uri): void
    {
        if ($scope = $this->getAnyScopes($method, $uri)) {
            $this->mockScope([$scope]);
        }
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
     * Mock Passport scopes.
     *
     * @param string|string[] $scopes
     */
    public function mockScope($scopes): void
    {
        if (is_string($scopes)) {
            $scopes = [$scopes];
        }

        $this->passportUser = forward_static_call_array([ClassResolver::getModelClass("User"), "random"], []);

        Passport::actingAs($this->passportUser, $scopes);
    }
}
