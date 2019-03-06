<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;
use Mathrix\Lumen\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Exceptions\Http\Http401UnauthorizedException;
use Mathrix\Lumen\Exceptions\Models\ValidationException;
use Mathrix\Lumen\Responses\PaginationJsonResponse;

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
     * @throws Http400BadRequestException
     */
    public function __construct()
    {
        $modelName = str_replace(["App\\Controllers\\", "Controller"], "", \get_class($this));
        $modelClass = $this->resolveModelClass($modelName, false);

        if ($modelClass !== false) {
            $this->modelClass = $modelClass;
        }
    }

    /**
     * Resolve a model class by a given controller name.
     * @param string $name the model name
     * @param bool $exceptionOnFail if true, raise and exception
     *
     * @return BaseModel|bool
     *
     * @throws Http400BadRequestException
     */
    protected function resolveModelClass(string $name, $exceptionOnFail = true)
    {
        $potentialModel = $this->modelClass ?? Str::studly(Str::singular($name));

        /** @var BaseModel $potentialModelClass */
        $potentialModelClass = "App\\Models\\$potentialModel";

        if (!class_exists($potentialModelClass) && $exceptionOnFail) {
            dd($potentialModelClass);
            throw new Http400BadRequestException("$potentialModelClass does not exist"); // @codeCoverageIgnore
        } elseif (!class_exists($potentialModelClass) && !$exceptionOnFail) {
            return false;
        }

        return $potentialModelClass;
    }

    /**
     * Generic paginated index method.
     *
     * @param Request $request The request
     * @param int $page
     * @param int $perPage
     *
     * @throws Http401UnauthorizedException
     *
     * @return PaginationJsonResponse
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
     * @throws Http401UnauthorizedException
     *
     * @return JsonResponse
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
     * @throws Http401UnauthorizedException
     * @throws ValidationException
     *
     * @return JsonResponse
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
     * @throws Http401UnauthorizedException
     *
     * @return JsonResponse
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
     * @throws Http401UnauthorizedException
     * @throws \Exception
     *
     * @return JsonResponse
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
        $relatedModelClass = "App\\Models\\$what";
        $ucName = ucfirst($what);

        if (!class_exists($relatedModelClass)) {
            throw new Http400BadRequestException([], "$relatedModelClass does not exists.");
        }

        /** @var BaseModel $model */
        $model = $relatedModelClass::query()->findOrFail($id);

        $potentialRelation = Str::lower($this->modelClass::getTableName());

        if (!method_exists($model, $potentialRelation)) {
            throw new Http400BadRequestException("$relatedModelClass::$potentialRelation() does not exist");
        }

        if ($request->user() !== null && $request->user()->cant("by$ucName", $this->modelClass)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass
            ], "Failed to pass by$ucName policy");
        }

        $query = $model->{$potentialRelation}();
        return new PaginationJsonResponse($query, $page, $perPage);
    }
}
