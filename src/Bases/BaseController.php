<?php

namespace Mathrix\Lumen\Bases;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;
use Mathrix\Lumen\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Exceptions\Http\Http401UnauthorizedException;
use Mathrix\Lumen\Exceptions\ValidationException;
use Mathrix\Lumen\Responses\PaginationJsonResponse;
use Mathrix\Lumen\Utils\ClassResolver;

/**
 * Class BaseController.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseController extends Controller
{
    /** @var BaseModel The model class associated with the controller */
    public $modelClass = null;


    /**
     * BaseController constructor
     * Build model class.
     */
    public function __construct()
    {
        $this->modelClass = ClassResolver::getModelClassFrom("Controller", get_class($this));
    }


    /**
     * Validate the given request with the given rules.
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = ValidatorFacade::make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            throw new ValidationException($errors, "Model data failed to pass validation.");
        }

        return $this->extractInputFromRules($request, $rules);
    }


    /**
     * Generic paginated index method.
     *
     * @param Request $request The request
     * @param int $page
     * @param int $perPage
     *
     * @return PaginationJsonResponse
     * @throws Http401UnauthorizedException
     *
     */
    public function index(Request $request, int $page = 0, int $perPage = 100): PaginationJsonResponse
    {
        // Check permission
        if ($request->user() !== null && $request->user()->cant("index", $this->modelClass)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass index policy");
        }

        return new PaginationJsonResponse($this->modelClass::query(), $page, $perPage);
    }


    /**
     * Generic get action.
     *
     * @param Request $request The request
     * @param int $id The model id
     *
     * @return JsonResponse
     * @throws Http401UnauthorizedException
     *
     */
    public function get(Request $request, int $id): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::query()->findOrFail($id);

        if ($request->user() !== null && $request->user()->cant("get", $model)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass get policy");
        }

        return new JsonResponse($model);
    }


    /**
     * Generic post action.
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     *
     * @throws Http401UnauthorizedException
     */
    public function post(Request $request): JsonResponse
    {
        if ($request->user() !== null && $request->user()->cant("post", $this->modelClass)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass post policy");
        }

        /** @var BaseModel $model */
        $model = new $this->modelClass($request->all());
        $model->save();

        return new JsonResponse($model);
    }


    /**
     * Generic edit action.
     *
     * @param Request $request The request
     * @param int $id The model id
     *
     * @return JsonResponse
     * @throws Http401UnauthorizedException
     *
     */
    public function patch(Request $request, int $id): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::query()->findOrFail($id);

        if ($request->user() !== null && $request->user()->cant("patch", $model)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass patch policy");
        }

        $model->update($request->all());

        return new JsonResponse($model);
    }


    /**
     * Generic delete action.
     *
     * @param Request $request The request
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     *
     * @throws Http401UnauthorizedException
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::query()->findOrFail($id);

        if ($request->user() !== null && $request->user()->cant("delete", $model)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass delete policy");
        }

        $model->delete();

        return new JsonResponse([
            "success" => true,
            "message" => ucfirst(Str::singular($this->modelClass::getTableName())) . " id $id was successfully deleted.",
        ]);
    }


    /**
     * Get the models which satisfy a given relation.
     *
     * @param Request $request The request
     * @param string $what the linked model
     * @param int $id the linked model id
     * @param int $page the query page
     * @param int $perPage the query per page
     *
     * @return PaginationJsonResponse
     * @throws Http400BadRequestException
     * @throws Http401UnauthorizedException
     */
    public function by(Request $request, string $what, int $id, int $page = 0, int $perPage = 100):
    PaginationJsonResponse
    {
        /** @var BaseModel $relatedModelClass */
        $relatedModelName = Str::ucfirst($what);
        $relatedModelClass = ClassResolver::getModelClass($relatedModelName);

        if (!class_exists($relatedModelClass)) {
            throw new Http400BadRequestException([], "$relatedModelClass does not exists.");
        }

        /** @var BaseModel $model */
        $model = $relatedModelClass::query()->findOrFail($id);

        $potentialRelation = Str::lower($this->modelClass::getTableName());

        if (!method_exists($model, $potentialRelation)) {
            throw new Http400BadRequestException("$relatedModelClass::$potentialRelation() does not exist");
        }

        if ($request->user() !== null && $request->user()->cant("by$relatedModelName", $this->modelClass)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass by$relatedModelName policy");
        }

        $query = $model->{$potentialRelation}();
        return new PaginationJsonResponse($query, $page, $perPage);
    }
}
