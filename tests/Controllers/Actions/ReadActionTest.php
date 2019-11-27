<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers\Actions;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Actions\ReadAction;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use function get_class;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Controllers\Actions\ReadAction
 */
class ReadActionTest extends MockeryTestCase
{
    /**
     * @covers ::defaultRead
     */
    public function testDefaultRead(): void
    {
        // Static data
        $keyName    = 'id';
        $identifier = 1;
        $request    = Request::create("/mangos/$identifier", 'GET');

        // Mock QueryExtractor
        $extractor = Mockery::mock('overload:' . QueryExtractor::class);
        $extractor->shouldReceive('getKey')->withNoArgs()->andReturn($keyName)->once();
        $extractor->shouldReceive('getWith')->withNoArgs()->andReturn([])->once();

        // Mock BaseModel (\Pear)
        $model = Mockery::mock('overload:\Mango');
        $model->shouldReceive('load')->with([]);

        // Mock Builder
        $query = Mockery::mock();
        $query->shouldReceive('where')->with($keyName, '=', $identifier)->andReturnSelf()->once();
        $query->shouldReceive('firstOrFail')->withNoArgs()->andReturn($model)->once();

        // Mock DataResponse
        Mockery::mock('overload:' . DataResponse::class);

        // Setup ReadAction trait
        /** @var ReadAction|MockInterface $trait */
        $trait = Mockery::mock(ReadAction::class);
        $trait->shouldReceive('query')->andReturn($query);
        $trait->shouldReceive('canOrFail')->with($request, 'read', $model)->andReturnNull();
        $trait->modelClass = get_class($model);

        $trait->defaultRead($request, $identifier);
    }
}
