<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers\Actions;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Wrapper;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;

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
        $wrapper = new Wrapper($request, $this->modelClass);

        /** @var BaseModel $model */
        $model = new $this->modelClass($request->all());

        $this->canOrFail($request, 'create', $model);
        if ($wrapper->hasExpand()) {
            
        }

        $model->save();
        $model->load($wrapper->getWith());

        return new DataResponse($model);
    }
}
