<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers\Actions;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Actions\CreateAction;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use function get_class;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Controllers\Actions\CreateAction
 */
class CreateActionTest extends MockeryTestCase
{
    /**
     * @covers ::defaultCreate
     */
    public function testDefaultCreate(): void
    {
        // Static data
        $data    = ['name' => 'golden'];
        $request = Request::create('/apples', 'post', $data);

        // Mock QueryExtractor
        $extractor = Mockery::mock('overload:' . QueryExtractor::class);
        $extractor->shouldReceive('getWith')->withNoArgs()->andReturn([])->once();

        // Mock BaseModel (\Apple)
        $model = Mockery::mock('overload:\Apple');
        $model->shouldReceive('fill')->with($data)->once();
        $model->shouldReceive('save')->withNoArgs()->andReturnNull()->once();
        $model->shouldReceive('load')->with([])->andReturnNull()->once();
        $model->shouldReceive('refresh')->withNoArgs()->andReturnSelf()->once();

        // Mock DataResponse
        Mockery::mock('overload:' . DataResponse::class);

        // Setup CreateAction trait
        /** @var CreateAction|MockInterface $trait */
        $trait = Mockery::mock(CreateAction::class);
        $trait->shouldReceive('canOrFail')->andReturnNull()->once(); // cannot test args
        $trait->modelClass = get_class($model);

        $trait->defaultCreate($request);
    }
}
