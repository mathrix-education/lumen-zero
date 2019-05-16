<?php

namespace Mathrix\Lumen\Zero\Controllers;

use Illuminate\Container\BoundMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as LumenController;
use Mathrix\Lumen\Zero\Controllers\Actions\RelationGet;
use Mathrix\Lumen\Zero\Controllers\Actions\RelationPatch;
use Mathrix\Lumen\Zero\Controllers\Actions\StandardDelete;
use Mathrix\Lumen\Zero\Controllers\Actions\StandardGet;
use Mathrix\Lumen\Zero\Controllers\Actions\StandardIndex;
use Mathrix\Lumen\Zero\Controllers\Actions\StandardPatch;
use Mathrix\Lumen\Zero\Controllers\Actions\StandardPost;
use Mathrix\Lumen\Zero\Controllers\Traits\HasAbilities;
use Mathrix\Lumen\Zero\Controllers\Traits\HasRequestValidator;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequestException;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use ReflectionException;

/**
 * Class BaseController.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
abstract class BaseController extends LumenController
{
    use StandardIndex, StandardPost, StandardGet, StandardPatch, StandardDelete, RelationGet, RelationPatch,
        HasAbilities, HasRequestValidator;

    /** @var Request $request The Illuminate HTTP request */
    protected $request;
    /** @var BaseModel The associated model class. */
    protected $modelClass = null;


    /**
     * BaseController constructor
     * Build model class.
     */
    public function __construct()
    {
        $this->modelClass = $this->modelClass ?? ClassResolver::getModelClass($this);
    }


    /**
     * A standard route has the following shape:
     * GET /models
     * POST /models
     * GET/PATH/DELETE /models/{identifier}
     * GET/PATH/DELETE /models/{key}/{identifier}
     *
     * A relation route has the following shape:
     * GET /models/{identifier}/{relation}
     * GET /models/{key}/{identifier}/{relation}
     * PATCH /models/{identifier}/{relation}
     *
     * @param array $args The request arguments.
     *
     * @return JsonResponse|Response
     * @throws Http400BadRequestException
     * @throws ReflectionException
     */
    public function __invoke(array $args)
    {
        $this->request = app()->make(Request::class);

        [$action, $args] = $this->prepareRESTRequest();

        return BoundMethod::call(app(), [$this, $action], $args, null);
    }


    /**
     * Prepare REST request: make [$action, $args].
     *
     * @return array
     * @throws Http400BadRequestException
     */
    protected function prepareRESTRequest()
    {
        $args = func_get_args();
        // We split the request using the '/' as delimiter
        $parts = explode("/", trim($this->request->getRequestUri(), "/"));

        // Build args
        $method = strtolower($this->request->method());
        $makeAction = function ($method, $relation) {
            return $relation . ucfirst($method);
        };

        switch (count($parts)) {
            case 1:
                $action = $makeAction($method === "get" ? "index" : $method, "standard");
                break;
            case 2:
            case 3:
            case 4:
                if (!$this->isRelationAction($parts)) {
                    $key = isset($parts[2]) ? $parts[1] : with(new $this->modelClass)->getKeyName();
                    $value = isset($parts[2]) ? $parts[2] : $parts[1];
                    $args = array_merge([$key, $value], $args);
                    $action = $makeAction($method, "standard");
                } else {
                    $key = isset($parts[3]) ? $parts[1] : with(new $this->modelClass)->getKeyName();
                    $value = isset($parts[3]) ? $parts[2] : $parts[1];
                    $relation = isset($parts[3]) ? $parts[3] : $parts[2];
                    $args = array_merge([$key, $value, $relation], $args);
                    $action = $makeAction($method, "relation");
                }
                break;
            default:
                throw new Http400BadRequestException();
        }

        return [$action, $args];
    }


    /**
     * We need to determine if the action is a "relation" action, or a standard action. Most of time, the number of
     * "uri-parts" is sufficient. The only edge case is the following:
     * (A): /cakes/slug/cheese-cake VS (B): /cakes/17/ingredients (both 3 args)
     * Fortunately, the third "uri-part" is a method of the model Cake, which help us to cover this edgy case.
     *
     * @param array $parts
     *
     * @return bool
     */
    protected function isRelationAction(array $parts)
    {
        if (count($parts) < 3) {
            return false;
        } else if (count($parts) > 3) {
            return true;
        } else {
            // Edgy case: count($args) === 3
            return method_exists($this->modelClass, $parts[2]);
        }
    }
}
