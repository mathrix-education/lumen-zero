<?php

namespace Mathrix\Lumen\Zero\Utils;

use Mathrix\Lumen\Zero\Testing\DataProvider;
use Mathrix\Lumen\Zero\Testing\ModelMockFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class RESTResolverTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class RESTUtilsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        ModelMockFactory::make()
            ->setName("Apple")
            ->setMethod("public", "getKeyName", "id")
            ->setMethod("public", "brands", null)
            ->compile()
            ->exec();
    }


    public function resolveDataProvider()
    {
        return DataProvider::makeDataProvider([
            // Standard
            "std:index" => ["get", "apples", "id", null],
            "std:post" => ["post", "apples", "id", null],
            "std:get" => ["get", "apples/{appleId}", "id", null],
            "std:patch" => ["patch", "apples/{appleId}", "id", null],
            "std:delete" => ["delete", "apples/{appleId}", "id", null],
            "std:get:slug" => ["get", "apples/slug/{appleSlug}", "slug", null],
            "std:patch:slug" => ["patch", "apples/slug/{appleSlug}", "slug", null],

            // Relations
            "rel:get:brands" => ["get", "apples/{appleId}/brands", "id", "brands"],
            "rel:patch:brands" => ["patch", "apples/{appleId}/brands", "id", "brands"],
            "rel:get:slug:brands" => ["get", "apples/slug/{appleSlug}/brands", "slug", "brands"],
            "rel:patch:slug:brands" => ["patch", "apples/slug/{appleSlug}/brands", "slug", "brands"],
        ]);
    }


    /**
     * @param string $key
     * @param string $expectedMethod
     * @param string $expectedUri
     * @param string $expectedField
     * @param string $expectedRelation
     *
     * @dataProvider resolveDataProvider
     * @covers       \Mathrix\Lumen\Zero\Utils\RESTUtils::resolve
     */
    public function testResolve(string $key, string $expectedMethod, string $expectedUri, string $expectedField,
                                ?string $expectedRelation)
    {
        [$method, $uri, $field, $relation] = RESTUtils::resolve(ClassResolver::getModelClass("Apple"), $key);
        $this->assertEquals($expectedMethod, $method);
        $this->assertEquals($expectedUri, $uri);
        $this->assertEquals($expectedField, $field);
        $this->assertEquals($expectedRelation, $relation);
    }
}
