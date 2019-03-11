<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Console\Command;


/**
 * Class BaseCommand.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseCommand extends Command
{
    public function success(string $string, $verbosity = null)
    {
        $this->line("[<info>✔</info>] $string", null, $verbosity);
    }


    public function fatal(string $string, $verbosity = null)
    {
        $this->block($string, "error", $verbosity);
        exit(1);
    }


    public function block(string $string, $style = null, $verbosity = null)
    {
        $string = " $string ";
        $l = str_repeat(" ", strlen($string));

        $this->line($l, $style, $verbosity);
        $this->line($string, $style, $verbosity);
        $this->line($l, $style, $verbosity);
    }
}
