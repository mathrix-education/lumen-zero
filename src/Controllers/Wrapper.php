<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Models\BaseModel;
use function explode;
use function strlen;
use function substr;
use function with;

class Wrapper
{
    public const MAX_LIMIT        = 100;
    public const SORT_OPERATORS   = ['+', '-'];
    public const FILTER_OPERATORS = ['=', '<', '>', '<=', '>=', '%', '!='];

    /** @var Request The incoming Illuminate request */
    private $request;
    /** @var BaseModel|string The parent controller model class. */
    private $modelClass;
    /** @var string $key The model used for CRUD operations. */
    private $key;
    /** @var string[] The Eloquent query builder with. */
    private $with;
    /** @var array The Eloquent query builder conditions */
    private $wheres = [];
    /** @var int The Eloquent query builder limit */
    private $limit = self::MAX_LIMIT;
    /** @var int The Eloquent query builder offset */
    private $offset = 0;
    /** @var string The order column */
    private $orderColumn = 'id';
    /** @var string The order direction */
    private $orderDirection = 'asc';

    public function __construct(Request $request, string $modelClass)
    {
        $this->request    = $request;
        $this->modelClass = $modelClass;
        $this->parse();
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string[]
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /**
     * If the query has an expand querystring.
     *
     * @return bool
     */
    public function hasExpand(): bool
    {
        return !empty($this->with);
    }

    /**
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getOrderColumn(): string
    {
        return $this->orderColumn;
    }

    /**
     * @return string
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * Extract an operator from a input string.
     *
     * @param string   $input           The input string.
     * @param string[] $operators       The allowed operators.
     * @param string   $defaultOperator The default operator.
     *
     * @return array [operator, value]
     */
    public function extract(string $input, array $operators, string $defaultOperator): array
    {
        foreach ($operators as $operator) {
            if (Str::startsWith($input, $operator)) {
                $value = substr($input, strlen($operator));

                return [$operator, $value];
            }
        }

        return [$defaultOperator, $input];
    }

    /**
     * Parse the Illuminate request query string.
     */
    private function parse(): void
    {
        // Setup the request boundaries
        $this->key    = $this->request->query('key', with(new $this->modelClass())->getKeyName());
        $this->limit  = $this->request->query('per_page', self::MAX_LIMIT);
        $this->offset = $this->request->query('page', 0) * $this->limit;

        // Order
        [$this->orderDirection, $this->orderColumn] = $this->extract(
            $this->request->query('sort', '+' . $this->getKey()),
            self::SORT_OPERATORS,
            '+'
        );

        // Patch the order from +/- to asc/desc
        $this->orderDirection = $this->orderDirection === '+' ? 'asc' : 'desc';

        // Conditions
        $searchColumns = $this->modelClass::getSearchableColumns();
        foreach ($searchColumns as $key) {
            if ($this->request->query('query') !== null) {
                $this->wheres[] = [$key, 'LIKE', '%' . $this->request->query('query') . '%', 'or'];
            } else {
                $queryStringValue = $this->request->query($key);

                // Skip if not provided in query string
                if ($queryStringValue === null) {
                    continue;
                }

                [$operator, $value] = $this->extract(
                    $queryStringValue,
                    self::FILTER_OPERATORS,
                    '='
                );

                $this->wheres[] = [$key, $operator, $value];
            }
        }

        // Expand and with
        $expand     = $this->request->query('expand', null);
        $this->with = !empty($expand) ? explode(',', $expand) : [];
    }
}
