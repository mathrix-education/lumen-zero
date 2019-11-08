<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers\Actions;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\Actions\CreateAction;
use Mathrix\Lumen\Zero\Models\BaseModel;
use Mathrix\Lumen\Zero\Responses\DataResponse;
use PHPUnit\Framework\TestCase;
use function get_class;

/**
 * @coversDefaultClass \Mathrix\Lumen\Zero\Controllers\Actions\CreateAction
 */
class CreateActionTest extends TestCase
{
    /**
     * @covers ::defaultCreate
     */
    public function testDefaultCreate(): void
    {
        /** @var CreateAction $trait */
        $trait = $this->getMockForTrait(
            CreateAction::class,
            [],
            '',
            true,
            true,
            true,
            ['canOrFail']
        );

        /** @var BaseModel $modelMock */
        $modelMock = $this->getMockForAbstractClass(
            BaseModel::class,
            [],
            '',
            true,
            true,
            true,
            ['save', 'load']
        );

        $trait->modelClass = get_class($modelMock);

        $request = Request::create('/apples', 'post');
        $result  = $trait->defaultCreate($request);

        $this->assertInstanceOf(DataResponse::class, $result);
    }
}
