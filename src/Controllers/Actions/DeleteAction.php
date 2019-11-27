<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;

/**
 * @property string $modelClass
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
    final public function defaultDelete(Request $request, $identifier): DataResponse
    {
        $wrapper = new QueryExtractor($request, $this->modelClass);

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
