<?php
declare(strict_types=1);

if (!function_exists('mkdirp')) {
    /**
     * Create a directory, recursively.
     *
     * @param string $path
     * @param int    $mode
     * @param null   $context
     */
    function mkdirp(string $path, $mode = 0777, $context = null): void
    {
        if (is_dir($path) || file_exists($path)) {
            return;
        }

        if (!mkdir($path, $mode, true, $context) && !is_dir($path)) {
            throw new RuntimeException("Directory \"{$path}\" was not created");
        }
    }
}
