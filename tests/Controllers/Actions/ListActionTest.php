<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers\Actions;

use Illuminate\Http\Request;
use JsonSerializable;
use Mathrix\Lumen\Zero\Controllers\Actions\ListAction;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Responses\PaginationResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Controllers\Actions\ListAction
 */
class ListActionTest extends MockeryTestCase
{
    /**
     * @covers ::defaultList
     */
    public function testDefaultList(): void
    {
        // Static data
        $request = Request::create('/ananas', 'GET');

        // Mock QueryExtractor
        $extractor = Mockery::mock('overload:' . QueryExtractor::class);
        $extractor->shouldReceive(
            'getWith',
            'getWheres',
            'getOrderColumn',
            'getOrderDirection',
            'getLimit',
            'getOffset'
        );

        // Mock BaseModel (\Ananas)
        Mockery::mock('overload:\Ananas');

        // Mock Builder
        $query = Mockery::mock(JsonSerializable::class);
        $query->shouldReceive(
            'with',
            'where',
            'orderBy',
            'limit',
            'offset'
        )->andReturnSelf();

        // Mock PaginationResponse
        Mockery::mock('overload:' . PaginationResponse::class);

        // Setup ListAction trait
        /** @var ListAction|MockInterface $trait */
        $trait = Mockery::mock(ListAction::class);
        $trait->shouldReceive('canOrFail')->withArgs([$request, 'list', '\Ananas']);
        $trait->shouldReceive('query')->withNoArgs()->andReturn($query);
        $trait->modelClass = '\Ananas';

        $trait->defaultList($request);
    }
}
