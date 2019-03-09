<?php

namespace Mathrix\Lumen\Tests\Traits;

use Laravel\Passport\Passport;
use Mathrix\Lumen\Utils\ClassResolver;

/**
 * Class PassportTrait.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
trait PassportTrait
{
    /**
     * Mock Passport scopes.
     *
     * @param string[] ...$scopes
     */
    public function mockScope(...$scopes): void
    {
        $user = forward_static_call_array([ClassResolver::getModelClass("User"), "random"], []);

        Passport::actingAs($user, $scopes);
    }
}
