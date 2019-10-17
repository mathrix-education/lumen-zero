<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Wrapper;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;

/**
 * @method Builder query()
 */
trait DeleteAction
{
    /**
     * DELETE /{models}/{identifier}
     *
     * @param Request    $request    The Illuminate HTTP request.
     * @param string|int $identifier The model identifier.
     *
     * @return DataResponse
     *
     * @throws Exception
     */
    final protected function defaultDelete(Request $request, $identifier): DataResponse
    {
        $wrapper = new Wrapper($request, $this->modelClass);

        /** @var BaseModel $model */
        $model = $this->query()
            ->where($wrapper->getKey(), '=', $identifier)
            ->firstOrFail();

        $this->canOrFail($request, 'delete', $model);

        $model->delete();

        return new DataResponse([
            $model->getKeyName() => $model->getKey(),
        ]);
    }
}
