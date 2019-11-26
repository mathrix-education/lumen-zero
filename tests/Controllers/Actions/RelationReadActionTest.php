<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers\Actions;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Actions\RelationReadAction;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use Mathrix\Lumen\Zero\Responses\PaginationResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use function get_class;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Controllers\Actions\RelationReadAction
 */
class RelationReadActionTest extends MockeryTestCase
{
    private const KEY_NAME      = 'id';
    private const IDENTIFIER    = 1;
    private const RELATION_NAME = 'brands';

    private function getExtractorMock(string $relationClass)
    {
        // Mock QueryExtractor
        $extractor = Mockery::mock('overload:' . QueryExtractor::class);
        $extractor->shouldReceive('getKey')->withNoArgs()->andReturn(self::KEY_NAME)->once();
        $extractor->shouldReceive('getWith')->withNoArgs()->andReturn([])->once();

        if ($relationClass !== BelongsTo::class) {
            $extractor->shouldReceive('getWheres')->withNoArgs()->andReturn([])->once();
            $extractor->shouldReceive('getOrderColumn')->withNoArgs()->andReturn('id')->once();
            $extractor->shouldReceive('getOrderDirection')->withNoArgs()->andReturn('asc')->once();
            $extractor->shouldReceive('getLimit')->withNoArgs()->andReturn(100)->once();
            $extractor->shouldReceive('getOffset')->withNoArgs()->andReturn(0)->once();
        }

        return $extractor;
    }

    private function getBaseRelationMock(string $relationClass)
    {
        $relation = Mockery::mock($relationClass);
        $relation->shouldReceive('with')->with([])->andReturnSelf()->once();

        if ($relationClass !== BelongsTo::class) {
            $relation->shouldReceive('where')->with([])->andReturnSelf()->once();
            $relation->shouldReceive('orderBy')->with('id', 'asc')->andReturnSelf()->once();
            $relation->shouldReceive('limit')->with(100)->andReturnSelf()->once();
            $relation->shouldReceive('offset')->with(0)->andReturnSelf()->once();
        }

        return $relation;
    }

    public function provider()
    {
        return [[BelongsTo::class], [HasMany::class], [BelongsToMany::class]];
    }

    /**
     * @param string $relationClass
     *
     * @dataProvider provider
     * @covers ::defaultRelationRead
     */
    public function testDefaultRelationReadBelongsTo(string $relationClass): void
    {
        // Mock Model
        $model = Mockery::mock('overload:\Kiwi');
        $model->shouldReceive(self::RELATION_NAME)
            ->withNoArgs()
            ->andReturn($this->getBaseRelationMock($relationClass))
            ->once();

        $this->getExtractorMock($relationClass);

        // Mock Builder
        $query = Mockery::mock();
        $query->shouldReceive('where')
            ->with(self::KEY_NAME, '=', self::IDENTIFIER)
            ->andReturnSelf()
            ->once();
        $query->shouldReceive('firstOrFail')->andReturn($model);

        // Setup trait
        $trait = Mockery::mock(RelationReadAction::class);
        $trait->shouldReceive('query')->withNoArgs()->andReturn($query)->once();
        $trait->modelClass = get_class($model);

        Mockery::mock('overload:' . PaginationResponse::class);

        $result = $trait->defaultRelationRead(
            Request::create('/kiwis/' . self::RELATION_NAME, 'GET'),
            self::IDENTIFIER,
            self::RELATION_NAME
        );

        if ($relationClass === BelongsTo::class) {
            $this->assertInstanceOf(DataResponse::class, $result);
        } else {
            $this->assertInstanceOf(PaginationResponse::class, $result);
        }
    }
}
