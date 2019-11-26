<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers\Actions;

use Exception;
use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Actions\DeleteAction;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Controllers\Actions\DeleteAction
 */
class DeleteActionTest extends MockeryTestCase
{
    /**
     * @throws Exception
     *
     * @covers ::defaultDelete
     */
    public function testDefaultDelete(): void
    {
        // Static data
        $keyName = 'id';
        $id      = 1;
        $request = Request::create("/pears/{$id}", 'DELETE');

        // Mock QueryExtractor
        $extractor = Mockery::mock('overload:' . QueryExtractor::class);
        $extractor->shouldReceive('getKey')->withNoArgs()->andReturn($keyName)->once();

        // Mock BaseModel (\Pear)
        $model = Mockery::mock('overload:\Pear');
        $model->shouldReceive('delete')->withNoArgs()->andReturnNull()->once();
        $model->shouldReceive('getKeyName')->withNoArgs()->andReturn($keyName)->once();
        $model->shouldReceive('getKey')->withNoArgs()->andReturn($id)->once();

        // Mock Builder
        $query = Mockery::mock();
        $query->shouldReceive('where')->with($keyName, '=', $id)->andReturnSelf()->once();
        $query->shouldReceive('firstOrFail')->withNoArgs()->andReturn($model)->once();

        // Mock DataResponse
        Mockery::mock('overload:' . DataResponse::class);

        // Setup DeleteAction
        $trait = Mockery::mock(DeleteAction::class);
        $trait->shouldReceive('query')->andReturn($query)->once();
        $trait->shouldReceive('canOrFail')
            ->with($request, 'delete', $model)
            ->andReturnNull()
            ->once();
        $trait->modelClass = get_class($model);

        $trait->defaultDelete($request, $id);
    }
}
