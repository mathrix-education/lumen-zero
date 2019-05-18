<?php

namespace Mathrix\Lumen\Zero\Console\Commands;

use Brick\VarExporter\VarExporter;
use Illuminate\Support\Collection;
use Mathrix\Lumen\Zero\Providers\RegistrarServiceProvider;

/**
 * Class RoutesCacheCommand.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 2.0.5
 */
class RoutesCacheCommand extends BaseCommand
{
    protected $signature = "routes:cache";
    protected $description = "Cache Lumen Zero routes.";


    public function handle()
    {
        $routes = Collection::make(app()->router->getRoutes());
        $this->line("Found {$routes->count()} routes.");

        $code = VarExporter::export($routes->toArray(), VarExporter::ADD_RETURN);
        $code = "<?php\n$code;";

        file_put_contents(
            app()->basePath(RegistrarServiceProvider::ROUTES_CACHE_FILE),
            $code
        );
    }
}
