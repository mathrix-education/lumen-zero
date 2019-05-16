<?php

namespace Mathrix\Lumen\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mathrix\Lumen\Zero\Controllers\BaseController;
use Mathrix\Lumen\Zero\Testing\DataProvider;
use Mathrix\Lumen\Zero\Testing\Dictionaries\Dictionary;
use Mathrix\Lumen\Zero\Testing\ModelMockFactory;
use Mathrix\Lumen\Zero\Testing\Traits\ReflectorTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class BaseControllerTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class BaseControllerTest extends TestCase
{
    use ReflectorTrait;

    /** @var MockObject|BaseController $subject */
    private $subject;


    /**
     * @return array
     * @throws Exception
     */
    public function prepareRESTRequestDataProvider()
    {
        $id = "17";
        $uuid = "f5b678a8-c43d-40bc-aaf4-8a0f3909177d";
        $slug = "super-slug";
        $relation = "brands";

        $data = [
            [
                ["standardIndex", []],
                "get", "/bananas"
            ]
        ];

        foreach (["get", "patch", "delete"] as $method) {
            $Method = ucfirst($method);

            $data[] = [
                ["standard$Method", ["id", $id]],
                $method, "/bananas/$id"
            ];
            $data[] = [
                ["standard$Method", ["uuid", $uuid]],
                $method, "/bananas/$uuid", "uuid"
            ];
            $data[] = [
                ["standard$Method", ["slug", $slug]],
                $method, "/bananas/slug/$slug"
            ];
        }

        foreach (["get", "patch"] as $method) {
            $Method = ucfirst($method);

            $data[] = [
                ["relation$Method", ["id", $id, $relation]],
                $method, "/bananas/$id/$relation"
            ];
            $data[] = [
                ["relation$Method", ["uuid", $uuid, $relation]],
                $method, "/bananas/$uuid/$relation", "uuid"
            ];
            $data[] = [
                ["relation$Method", ["slug", $slug, $relation]],
                $method, "/bananas/slug/$slug/$relation"
            ];
        }

        return $data;
    }


    /**
     * Mock a call to BaseController::prepareRESTRequest(). Model class is autogenerated.
     *
     * @param array $expected The expected arguments.
     * @param string $method The method.
     * @param string $uri The uri.
     * @param string $modelKey The associated model class key.
     *
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider prepareRESTRequestDataProvider
     * @covers       \Mathrix\Lumen\Controllers\BaseController::prepareRESTRequest
     */
    public function testPrepareRESTRequest(array $expected, string $method, string $uri, string $modelKey = "id"): void
    {
        $dictionary = new Dictionary();
        $model = Str::ucfirst($dictionary->random());
        $controllerName = "{$model}Controller";

        $modelClass = ModelMockFactory::make()
            ->setName($model)
            ->setMethod("public", "getKeyName", $modelKey)
            ->setMethod("public", "brands", null)
            ->compile()
            ->exec()
            ->getClass();

        $request = Request::create($uri, $method);

        // Mock BananaController
        $subject = $this->getMockForAbstractClass(
            BaseController::class,
            [],
            $controllerName,
            false
        );

        // Force request
        $this->set($subject, "request", $request);
        $this->set($subject, "modelClass", $modelClass);

        $args = $this->invoke($subject, "prepareRESTRequest", []);

        $this->assertEquals($expected, $args);
    }


    /**
     * @return array
     */
    public function actionDataProvider()
    {
        return DataProvider::makeDataProvider([
            "standardIndex" => ["index", null, "standard", null],
            "standardGet" => ["get", "id", "standard", null],
            "standardPost" => ["post", "id", "standard", null],
            "standardPatch" => ["patch", "id", "standard", null],
            "standardDelete" => ["delete", "id", "standard", null],
            "getBySlug" => ["get", "slug", "standard", null],
            "patchBySlug" => ["patch", "slug", "standard", null],
            "deleteBySlug" => ["delete", "slug", "standard", null],
            "relationGet" => ["get", "id", "relation", "bananas"],
            "relationPatch" => ["patch", "id", "relation", "bananas"],
            "getBananasBySlug" => ["get", "slug", "relation", "bananas"],
            "patchBananasBySlug" => ["patch", "slug", "relation", "bananas"]
        ]);
    }


    /**
     * @param string $expected
     * @param string $method
     * @param string $field
     * @param string $type
     * @param string $relation
     *
     * @throws ReflectionException
     *
     * @dataProvider actionDataProvider
     * @covers       \Mathrix\Lumen\Zero\Controllers\BaseController::action
     */
    public function testAction(string $expected, string $method, ?string $field, string $type, ?string $relation)
    {
        $modelClass = ModelMockFactory::make()
            ->setName()
            ->setMethod("public", "getKeyName", "id")
            ->setMethod("public", "brands", null)
            ->compile()
            ->exec()
            ->getClass();

        // Mock BananaController
        /** @var MockObject|BaseController $subject */
        $subject = $this->getMockBuilder(BaseController::class)
            ->setMethods([$expected])
            ->setConstructorArgs([])
            ->getMockForAbstractClass();

        // Force request
        $this->set($subject, "modelClass", $modelClass);

        $actual = $this->invoke($subject, "action", [$method, $field, $type, $relation]);
        $this->assertEquals($expected, $actual);
    }
}