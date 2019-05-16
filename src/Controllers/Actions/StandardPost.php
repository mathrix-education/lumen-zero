<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Models\BaseModel;

/**
 * Trait StandardPost.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait StandardPost
{
    /**
     * POST /models
     *
     * @param Request $request The Illuminate HTTP request.
     *
     * @return JsonResponse
     */
    public function standardPost(Request $request): JsonResponse
    {
        /** @var BaseModel $model */
        $model = new $this->modelClass($request->all());

        $ability = $this->getAbility("post", "standard");
        $this->canOrFail($request, $ability, $model);

        $model->save();

        return new JsonResponse($model);
    }
}
