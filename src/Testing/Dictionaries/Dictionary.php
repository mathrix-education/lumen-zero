<?php

namespace Mathrix\Lumen\Zero\Testing\Dictionaries;

use Illuminate\Support\Collection;

/**
 * Class Dictionary.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.0
 *
 * @mixin Collection
 */
class Dictionary
{
    /** @var array The loaded dictionaries. */
    private static $loaded = [];
    /** @var string The class dictionary. */
    private $dictionary;


    public function __construct(string $file = __DIR__ . "/nouns_singular.txt")
    {
        $this->dictionary = $file;
        $this->load();
    }


    /**
     * Load a dictionary, which is a simple text file, one wor per line.
     *
     * @return Dictionary
     */
    public function load(): self
    {
        if (!isset(self::$loaded[$this->dictionary])) {
            $words = file_get_contents($this->dictionary);

            self::$loaded[$this->dictionary] = Collection::make(explode("\n", $words))
                ->reject(function ($word) {
                    return empty($word);
                });
        }

        return $this;
    }


    /**
     * Forward method call to the Collection
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call(string $name, $arguments)
    {
        return call_user_func_array([self::$loaded[$this->dictionary], $name], $arguments);
    }
}
