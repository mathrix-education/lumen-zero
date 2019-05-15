<?php

namespace Mathrix\Lumen\Unit\Controllers\Traits;

use Mathrix\Lumen\Zero\Controllers\Traits\HasAbilities;
use Mathrix\Lumen\Zero\Tests\Traits\ReflectorTrait;
use PHPUnit\Framework\MockObject\MockObject;
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

    /** @var MockObject|HasAbilitiesTest $subject */
    private $subject;


    /**
     * @throws ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockForTrait(HasAbilities::class);
    }


    /**
     * @throws ReflectionException
     */
    public function testGetAbility()
    {
        $fixtures = [
            "index" => ["index", "standard"],
            "get" => ["get", "standard", "id"],
            "patch" => ["patch", "standard", "id", null],
            "getBySlug" => ["get", "standard", "slug"],
            "getByFirstName" => ["get", "standard", "first_name"],
            "patchByLastName" => ["patch", "standard", "last_name", null],
            "getApples" => ["get", "relation", "id", "apples"],
            "getApplesBySlug" => ["get", "relation", "slug", "apples"],
            "patchApples" => ["patch", "relation", "id", "apples"],
            "patchApplesBySlug" => ["patch", "relation", "slug", "apples"]
        ];

        foreach ($fixtures as $expected => $args) {
            $actual = $this->invoke($this->subject, "getAbility", $args);
            $this->assertEquals($expected, $actual);
        }
    }
}
