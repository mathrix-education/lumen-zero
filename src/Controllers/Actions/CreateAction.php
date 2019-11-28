<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;

/**
 * @property string $modelClass
 */
trait CreateAction
{
    /**
     * POST /{models}
     *
     * @param Request $request The Illuminate HTTP request.
     *
     * @return DataResponse
     */
    final public function defaultCreate(Request $request): DataResponse
    {
        $wrapper = new QueryExtractor($request, $this->modelClass);

        /** @var BaseModel $model */
        $model = new $this->modelClass();
        $model->fill($request->all());

        $this->canOrFail($request, 'create', $model);

        $model->save();
        $model->load($wrapper->getWith());

        return new DataResponse($model->refresh()); // refresh is necessary to have the fully hydrated model
    }
}
