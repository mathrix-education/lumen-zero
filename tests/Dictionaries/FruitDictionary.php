<?php

namespace Mathrix\Lumen\Zero\Dictionaries;

use Illuminate\Support\Arr;

/**
 * Class FruitDictionary.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since
 */
class FruitDictionary
{
    public static $fruits = [
        "Apple",
        "Watermelon",
        "Orange",
        "Pear",
        "Cherry",
        "Strawberry",
        "Nectarine",
        "Grape",
        "Mango",
        "Blueberry",
        "Pomegranate",
        "Plum",
        "Banana",
        "Raspberry",
        "Mandarin",
        "Jackfruit",
        "Papaya",
        "Kiwi",
        "Pineapple",
        "Lime",
        "Lemon",
        "Apricot",
        "Grapefruit",
        "Melon",
        "Coconut",
        "Avocado",
        "Peach"
    ];
    public static $used = [];


    public static function random($unique = false)
    {
        $fruit = Arr::random(self::$fruits);

        if (!$unique) {
            return $fruit;
        } else {
            if (count(self::$used) === count(self::$fruits)) {
                throw new \Exception("Dictionary exhausted");
            }

            while (in_array($fruit, self::$used)) {
                $fruit = Arr::random(self::$fruits);
            }

            self::$used[] = $fruit;

            return $fruit;
        }
    }


    public static function reset()
    {
        self::$used = [];
    }
}
