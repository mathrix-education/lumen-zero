<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;

/**
 * @property string $modelClass
 * @method Builder query()
 */
trait ReadAction
{
    /**
     * GET /{models}/{identifier}
     *
     * @param Request    $request    The Illuminate HTTP request.
     * @param int|string $identifier The model identifier.
     *
     * @return DataResponse
     */
    final public function defaultRead(Request $request, $identifier): DataResponse
    {
        $wrapper = new QueryExtractor($request, $this->modelClass);

        /** @var BaseModel $model */
        $model = $this->query()
            ->where($wrapper->getKey(), '=', $identifier)
            ->firstOrFail();

        $this->canOrFail($request, 'read', $model);

        $model->load($wrapper->getWith());

        return new DataResponse($model);
    }
}
