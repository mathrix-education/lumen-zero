<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Console\Commands;

use Illuminate\Console\Command;

/**
 * Base class for Artisan commands.
 */
abstract class BaseCommand extends Command
{
    abstract public function handle(): void;
}
