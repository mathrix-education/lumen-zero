<?php

namespace Mathrix\Lumen\Zero\Responses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Exceptions\Http\Http400BadRequestException;

/**
 * Class PaginationJsonResponse.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 4.3.0
 */
class PaginationJsonResponse extends SuccessJsonResponse
{
    protected const MAX_PER_PAGE = 1000;


    /**
     * PaginationJsonResponse constructor.
     *
     * @param Builder|Relation $query
     * @param int $status The HTTP status code (default 200).
     * @param array $headers The custom HTTP headers.
     * @param int $options The json_encode function options.
     * @throws Http400BadRequestException
     */
    public function __construct($query, $status = 200, $headers = [], $options = 0)
    {
        [$page, $perPage] = self::getPagination();

        if ($perPage > self::MAX_PER_PAGE) {
            throw new Http400BadRequestException(
                ["max_per_page" => self::MAX_PER_PAGE],
                "The maximum page size is " . self::MAX_PER_PAGE
            );
        }

        $total = $query->count();
        $models = $query->limit($perPage)
            ->offset($page * $perPage)
            ->get();

        $meta = [
            "page" => $page,
            "per_page" => $perPage,
            "total" => $total
        ];

        parent::__construct($models, $meta, $status, $headers, $options);
    }


    /**
     * Get the pagination parameters, in an array form. It case be used like so:
     * [$page, $perPage] = $this->getPagination();
     *
     * @param Request|null $request The Illuminate HTTP request. If given, query string analysis will be executed on it
     * instead of the Container's one.
     * @return array
     */
    public static function getPagination(Request $request = null)
    {
        /** @var Request $request */
        $request = $request ?? app()->make(Request::class);

        return [
            $request->query("page", 0),
            $request->query("perPage", 100)
        ];
    }
}
