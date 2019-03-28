<?php

namespace Mathrix\Lumen\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Mathrix\Lumen\Bases\BaseController;
use Mathrix\Lumen\Bases\BaseMail;
use Mathrix\Lumen\Exceptions\Http\Http400BadRequestException;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class DebugController.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 *
 * @codeCoverageIgnore
 */
class DebugController extends BaseController
{
    /**
     * GET /debug/info
     *
     * Give some information about the current application and its environment.
     *
     * @return JsonResponse
     */
    public function info()
    {
        /** @var Client $passwordClient */
        $passwordClient = Client::query()->where("password_client", "=", 1)->firstOrFail();

        $data = [
            "environment" => env("APP_ENV"),
            "commit" => env("APP_VERSION"),
            "database" => env("DB_DATABASE"),
            "oauth" => [
                "client_id" => $passwordClient->id,
                "client_secret" => $passwordClient->secret
            ]
        ];

        return new JsonResponse($data);
    }


    /**
     * GET /debug/reset
     * Reset the database by refreshing the migrations and seeding again.
     *
     * @return JsonResponse
     *
     * @throws Http400BadRequestException
     */
    public function reset(): JsonResponse
    {
        if (app()->environment() !== "dev") {
            throw new Http400BadRequestException([
                "environment" => env("APP_ENV")
            ], "Database reset is only available in dev environment.");
        } else {
            $commands = [
                [
                    "command" => "migrate:refresh",
                    "options" => []
                ],
                [
                    "command" => "db:seed",
                    "options" => []
                ],
            ];

            $data = [];
            $output = new BufferedOutput();
            foreach ($commands as $command) {
                $exitCode = Artisan::call($command["command"], $command["options"], $output);
                $data[] = array_merge($command, [
                    "exit" => $exitCode,
                    "logs" => collect(explode("\n", $output->fetch()))
                        ->map(function ($line) {
                            return trim($line);
                        })
                        ->filter(function ($line) {
                            return !empty($line);
                        })
                        ->values()
                ]);
            }

            return new JsonResponse($data);
        }
    }


    /**
     * GET /debug/mail/{mail}/{namespace?}
     * Render email in the browser.
     *
     * @param string $mail
     * @param string $namespace
     * @return BaseMail
     */
    public function mail(string $mail, string $namespace = "App\\Mails"): BaseMail
    {
        $mailClassName = Str::studly($mail);
        /** @var BaseMail $mailClass */
        $mailClass = "$namespace\\$mailClassName";

        return $mailClass::mock();
    }
}
