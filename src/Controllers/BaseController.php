<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers;

use Illuminate\Container\BoundMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as LumenController;
use Mathrix\Lumen\Zero\Controllers\Actions\CreateAction;
use Mathrix\Lumen\Zero\Controllers\Actions\DeleteAction;
use Mathrix\Lumen\Zero\Controllers\Actions\ListAction;
use Mathrix\Lumen\Zero\Controllers\Actions\ReadAction;
use Mathrix\Lumen\Zero\Controllers\Actions\RelationReadAction;
use Mathrix\Lumen\Zero\Controllers\Actions\RelationReorderAction;
use Mathrix\Lumen\Zero\Controllers\Actions\UpdateAction;
use Mathrix\Lumen\Zero\Controllers\Traits\HasAbilities;
use Mathrix\Lumen\Zero\Controllers\Traits\HasRequestValidator;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Utils\ClassResolver;
use ReflectionException;
use function app;
use function array_splice;
use function count;
use function explode;
use function method_exists;
use function strtok;
use function strtolower;
use function trim;
use function ucfirst;

abstract class BaseController extends LumenController
{
    use ListAction;
    use CreateAction;
    use ReadAction;
    use UpdateAction;
    use DeleteAction;
    use RelationReadAction;
    use RelationReorderAction;
    use HasAbilities;
    use HasRequestValidator;

    /** @var Request $request The Illuminate HTTP request */
    protected $request;
    /** @var BaseModel The associated model class. */
    protected $modelClass = null;
    /** @var array The relation loaded with model. */
    protected $with = [];

    /**
     * BaseController constructor
     * Build model class.
     */
    public function __construct()
    {
        $this->modelClass = $this->modelClass ?? ClassResolver::getModelClass($this);
    }

    /**
     * A L-CRUD route has the following shape:
     * GET /models (list)
     * POST /models (create)
     * GET /models/{identifier} (read)
     * PATCH /models/{identifier} (update)
     * DELETE /models/{identifier} (delete)
     *
     * A relation route has the following shape:
     * GET /models/{identifier}/{relation} (relationRead)
     * PATCH /models/{identifier}/{relation} (relationReorder)
     *
     * @param array $args The request arguments.
     *
     * @return JsonResponse|Response
     *
     * @throws ReflectionException
     */
    public function __invoke(...$args)
    {
        [$default, $actual, $args] = BoundMethod::call(app(), [$this, 'getAction'], [$args]);

        /** @var string $action The action to execute. */
        $action = method_exists($this, $actual) ? $actual : $default;

        return BoundMethod::call(app(), [$this, $action], $args);
    }

    /**
     * Get an Eloquent query builder based on the current controller  associated model class.
     *
     * @return Builder
     */
    protected function query(): Builder
    {
        /** @var BaseModel $instance */
        $instance = (new $this->modelClass());

        return $instance->newQuery();
    }

    /**
     * @param Request $request The Illuminate HTTP request.
     * @param array   $args    The request path arguments.
     *
     * @return array
     */
    public function getAction(Request $request, array $args): ?array
    {
        // Get the request uri without the querystring.
        $uri = strtok($request->getRequestUri(), '?');
        // We split the request using the '/' as delimiter
        $parts = explode('/', trim($uri, '/'));

        // Build args
        $method = strtolower($request->method());

        switch (count($parts)) {
            case 1:
                switch ($method) {
                    case 'get':
                        return ['defaultList', 'list', $args];
                    case 'post':
                        return ['defaultCreate', 'create', $args];
                }
                break;
            case 2:
                switch ($method) {
                    case 'get':
                        return ['defaultRead', 'read', $args];
                    case 'patch':
                        return ['defaultUpdate', 'update', $args];
                    case 'delete':
                        return ['defaultDelete', 'delete', $args];
                }
                break;
            case 3:
                $relation = $parts[2];
                array_splice($args, 1, 0, $parts[2]);

                switch ($method) {
                    case 'get':
                        return ['defaultRelationRead', 'read' . ucfirst($relation), $args];
                    case 'patch':
                        $args[] = 'order';

                        return ['defaultRelationReorder', 'reorder' . ucfirst($relation), $args];
                }
        }

        return null;
    }
}
