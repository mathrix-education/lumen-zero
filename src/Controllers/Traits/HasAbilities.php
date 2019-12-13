<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Mathrix\Lumen\Zero\Exceptions\PolicyDenied;
use function get_class;
use function method_exists;

/**
 * Allow controllers to check the user authorization via policies.
 */
trait HasAbilities
{
    /**
     * Get a policy object based on the controller model class.
     *
     * @return mixed
     */
    protected function getPolicy()
    {
        return Gate::getPolicyFor($this->modelClass);
    }

    /**
     * Check if the Gate denies the request.
     *
     * @param Request $request The Illuminate HTTP request.
     * @param string  $ability The policy ability.
     * @param null    $model   The policy model.
     *
     * @throws PolicyDenied
     */
    protected function canOrFail(Request $request, string $ability, $model = null): void
    {
        if ($this->shouldUsePolicy($ability) && Gate::forUser($request->user())->denies($ability, $model)) {
            throw new PolicyDenied(get_class($this->getPolicy()), $ability);
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
        $policy = $this->getPolicy();

        return $policy !== null && method_exists($policy, $ability);
    }
}
