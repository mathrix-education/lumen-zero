<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Models\BaseModel;

/**
 * Trait StandardPatch.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait StandardPatch
{
    /**
     * PATCH /models/{identifier} (if key is "id")
     * PATCH /models/{key}/{identifier}
     *
     * @param Request $request The Illuminate HTTP request.
     * @param string $key The model key.
     * @param string|int $identifier The model identifier.
     *
     * @return JsonResponse
     */
    public function standardPatch(Request $request, string $key, $identifier): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $identifier);

        $ability = $this->getAbility("patch", "standard", $key);
        $this->canOrFail($request, "patch", $ability);

        $model->update($request->all());

        return new JsonResponse($model);
    }
}
