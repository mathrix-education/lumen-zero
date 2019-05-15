<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Models\BaseModel;

/**
 * Class StandardGet.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait StandardGet
{
    /**
     * GET /models/{identifier} (if key is "id")
     * GET /models/{key}/{identifier}
     *
     * @param Request $request The request
     * @param string $key
     * @param mixed $identifier
     *
     * @return JsonResponse
     */
    public function standardGet(Request $request, string $key, $identifier): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $identifier);

        $ability = $this->getAbility("get", "standard", $key);
        $this->canOrFail($request, $ability, $model);

        return new JsonResponse($model);
    }
}
