<?php

namespace Mathrix\Lumen\Controllers\Traits;

use Mathrix\Lumen\Zero\Controllers\Traits\HasAbilities;
use Mathrix\Lumen\Zero\Testing\DataProvider;
use Mathrix\Lumen\Zero\Testing\Traits\ReflectorTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class HasAbilitiesTest.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 */
class HasAbilitiesTest extends TestCase
{
    use ReflectorTrait;


    public function getAbilityDataProvider()
    {
        return DataProvider::makeDataProvider([
            "index" => ["index", "standard"],
            "get" => ["get", "standard"],
            "patch" => ["patch", "standard"],
            "getBySlug" => ["get", "standard", "slug"],
            "getByFirstName" => ["get", "standard", "first_name"],
            "patchByLastName" => ["patch", "standard", "last_name"],
            "getApples" => ["get", "relation", "id", "apples"],
            "getApplesBySlug" => ["get", "relation", "slug", "apples"],
            "patchApples" => ["patch", "relation", "id", "apples"],
            "patchApplesBySlug" => ["patch", "relation", "slug", "apples"]
        ]);
    }


    /**
     * @param $expected
     * @param $method
     * @param $type
     * @param string $field
     * @param null $relation
     *
     * @throws ReflectionException
     *
     * @dataProvider getAbilityDataProvider
     * @covers       \Mathrix\Lumen\Zero\Controllers\Traits\HasAbilities::getAbility
     */
    public function testGetAbility($expected, $method, $type, $field = "id", $relation = null)
    {
        $subject = $this->getMockForTrait(HasAbilities::class);
        $actual = $this->invoke($subject, "getAbility", [$method, $type, $field, $relation]);
        $this->assertEquals($expected, $actual);
    }
}
