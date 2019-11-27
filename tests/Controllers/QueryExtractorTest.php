<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Tests\Controllers;

use Illuminate\Http\Request;
use Mathrix\Lumen\Zero\Controllers\QueryExtractor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use function get_class;

class QueryExtractorTest extends MockeryTestCase
{
    public function test()
    {
        $model = Mockery::mock('overload:\Apple');
        $model->shouldReceive('getKeyName')->andReturn('id')->once();
        $model->shouldReceive('getSearchableColumns')->andReturn(['category_id'])->once();

        $request   = Request::create('/apples?page=0&per_page=20&sort=-price&expand=owners;brands&category_id=1');
        $extractor = new QueryExtractor($request, get_class($model));

        $this->assertEquals('id', $extractor->getKey());
        $this->assertEquals(0, $extractor->getOffset());
        $this->assertEquals(20, $extractor->getLimit());
        $this->assertEquals('price', $extractor->getOrderColumn());
        $this->assertEquals('desc', $extractor->getOrderDirection());
        $this->assertEquals([['category_id', '=', 1]], $extractor->getWheres());
        $this->assertSame(['owners', 'brands'], $extractor->getWith());
        $this->assertTrue($extractor->hasExpand());
    }
}
