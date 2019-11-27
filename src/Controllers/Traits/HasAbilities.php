<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Mathrix\Lumen\Zero\Exceptions\Http\Http401Unauthorized;
use function method_exists;

/**
 * Trait HasAbilities.
 */
trait HasAbilities
{
    /**
     * Check if the Gate denies the request.
     *
     * @param Request $request
     * @param string  $ability
     * @param null    $model
     *
     * @throws Http401Unauthorized
     */
    protected function canOrFail(Request $request, string $ability, $model = null)
    {
        if ($this->shouldUsePolicy($ability) && Gate::forUser($request->user())->denies($ability, $model)) {
            throw new Http401Unauthorized([
                'model_class' => $this->modelClass,
                'ability' => $ability,
            ], "Failed to pass $ability policy");
        }
    }

    /**
     * Check if the controller should use a policy for a given ability.
     *
     * @param string $ability The policy ability.
     *
     * @return bool
     */
    protected function shouldUsePolicy(string $ability): bool
    {
        $policyClass = Gate::getPolicyFor($this->modelClass);

        return $policyClass !== null && method_exists($policyClass, $ability);
    }
}
