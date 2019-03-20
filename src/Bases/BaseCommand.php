<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Console\Command;


/**
 * Class BaseCommand.
 * Base class for Artisan commands.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseCommand extends Command
{
    /**
     * Print a success.
     * @param string $string
     * @param null $verbosity
     */
    public function success(string $string, $verbosity = null)
    {
        $this->line("[<info>✔</info>] $string", null, $verbosity);
    }


    /**
     * Print an error, then exit.
     * @param string $string
     * @param null $verbosity
     * @param int $code The exit code (default to 1)
     */
    public function fatal(string $string, $verbosity = null, int $code = 1)
    {
        $this->block($string, "error", $verbosity);
        exit($code);
    }


    /**
     * Print a block in the console.
     * @param string $string
     * @param null $style
     * @param null $verbosity
     */
    public function block(string $string, $style = null, $verbosity = null)
    {
        $string = " $string ";
        $l = str_repeat(" ", strlen($string));

        $this->line($l, $style, $verbosity);
        $this->line($string, $style, $verbosity);
        $this->line($l, $style, $verbosity);
    }
}
