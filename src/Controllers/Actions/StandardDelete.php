<?php

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\SuccessJsonResponse;

/**
 * Trait StandardDelete.
 * Handles delete action.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
trait StandardDelete
{
    /**
     * DELETE /models/{identifier} (if key is "id")
     * DELETE /models/{key}/{identifier}
     *
     * @param Request $request The Illuminate HTTP request.
     * @param string $key The model key.
     * @param string|int $value The model value.
     *
     * @return SuccessJsonResponse
     * @throws Exception
     */
    public function standardDelete(Request $request, string $key, $value): SuccessJsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::findByOrFail($key, $value);

        $ability = $this->getAbility("delete", "relation", $key);
        $this->canOrFail($request, $ability, $model);

        $model->delete();

        $message = ucfirst(Str::singular($this->modelClass::getTableName())) . " `$key` $value was successfully deleted.";

        return new SuccessJsonResponse($message);
    }
}
