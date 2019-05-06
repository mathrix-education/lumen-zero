<?php

namespace Mathrix\Lumen\Bases;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;
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
    /** @var BaseModel|string The model class associated with the controller */
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
     * Check if the controller should use a policy for a given ability.
     * @param string $ability The policy ability.
     * @return bool
     */
    protected function shouldUsePolicy(string $ability): bool
    {
        $policyClass = Gate::getPolicyFor($this->modelClass);

        return $policyClass !== null && method_exists($policyClass, $ability);
    }


    /**
     * Check if the Gate denies the request.
     * @param Request $request
     * @param string $ability
     * @param null $model
     * @throws Http401UnauthorizedException
     */
    protected function canOrFail(Request $request, string $ability, $model = null)
    {
        if ($this->shouldUsePolicy($ability) && Gate::forUser($request->user())->denies($ability, $model)) {
            throw new Http401UnauthorizedException([
                "model_class" => $this->modelClass,
                "ability" => $ability
            ], "Failed to pass $ability policy");
        }
    }


    /**
     * Generic paginated index method.
     *
     * @param Request $request The Illuminate HTTP request.
     * @param int $page The results page (starts at 0).
     * @param int $perPage The number of related models per page.
     *
     * @return PaginationJsonResponse
     * @throws Http401UnauthorizedException
     */
    public function index(Request $request, int $page = 0, int $perPage = 100): PaginationJsonResponse
    {
        $this->canOrFail($request, "index", $this->modelClass);
        return new PaginationJsonResponse($this->modelClass::query(), $page, $perPage);
    }


    /**
     * Generic get action.
     *
     * @param Request $request The request
     * @param int $id The model id.
     *
     * @return JsonResponse
     * @throws Http401UnauthorizedException
     *
     */
    public function get(Request $request, int $id): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::query()->findOrFail($id);
        $this->canOrFail($request, "get", $model);

        return new JsonResponse($model);
    }


    /**
     * Generic post action.
     *
     * @param Request $request The Illuminate HTTP request.
     *
     * @return JsonResponse
     *
     * @throws Http401UnauthorizedException
     */
    public function post(Request $request): JsonResponse
    {
        $this->canOrFail($request, "post", $this->modelClass);

        /** @var BaseModel $model */
        $model = new $this->modelClass($request->all());
        $model->save();

        return new JsonResponse($model);
    }


    /**
     * Generic edit action.
     *
     * @param Request $request The Illuminate HTTP request.
     * @param int $id The model id.
     *
     * @return JsonResponse
     * @throws Http401UnauthorizedException
     *
     */
    public function patch(Request $request, int $id): JsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::query()->findOrFail($id);
        $this->canOrFail($request, "patch", $this->modelClass);

        $model->update($request->all());

        return new JsonResponse($model);
    }


    /**
     * Generic delete action.
     *
     * @param Request $request The Illuminate HTTP request.
     * @param int $id The model id.
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

        $this->canOrFail($request, "delete", $this->modelClass);
        $model->delete();

        return new JsonResponse([
            "success" => true,
            "message" => ucfirst(Str::singular($this->modelClass::getTableName())) . " id $id was successfully deleted.",
        ]);
    }


    /**
     * Generic relation action.
     *
     * @param Request $request The Illuminate HTTP request.
     * @param string $relation The relation.
     * @param int $id The model id.
     * @param int $page The results page (starts at 0).
     * @param int $perPage The number of related models per page.
     *
     * @return PaginationJsonResponse
     * @throws Http401UnauthorizedException
     */
    public function relation(Request $request, string $relation, int $id, int $page, int $perPage): PaginationJsonResponse
    {
        /** @var BaseModel $model */
        $model = $this->modelClass::query()->findOrFail($id);

        $this->canOrFail($request, "delete", $this->modelClass);
        $related = $model->{$relation}();

        return new PaginationJsonResponse($related, $page, $perPage);
    }
}
