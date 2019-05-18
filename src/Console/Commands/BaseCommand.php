<?php

namespace Mathrix\Lumen\Zero\Console\Commands;

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
    abstract public function handle();
}
