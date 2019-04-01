<?php

namespace Mathrix\Lumen\Services\OpenAPI;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Parser.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 5.0.0
 */
class Parser
{
    /** @var string The schema paths */
    private $schemaPath;


    public function __construct($schemaPath = "docs/schemas")
    {
        $this->schemaPath = $schemaPath;
    }


    /**
     * Check if a json-schema exists.
     *
     * @param string $schema The schema name.
     *
     * @return bool
     */
    public function hasSchema(string $schema): bool
    {
        $file = app()->basePath($this->schemaPath . "/$schema.yaml");

        return file_exists($file);
    }


    /**
     * Get a JSON Schema.
     * @param string $schema
     *
     * @return array
     */
    public function getSchema(string $schema): array
    {
        $file = app()->basePath($this->schemaPath . "/$schema.yaml");

        return $this->preProcess(Yaml::parseFile($file));
    }


    /**
     * Get a JSON Schema wrapped into the array form.
     * @param string $schema
     * @return array
     */
    public function getSchemaArray(string $schema)
    {
        return [
            "type" => "array",
            "items" => $this->getSchema($schema)
        ];
    }


    /**
     * Get a JSON Schema wrapped into the paginated form.
     * @param string $schema
     * @return array
     */
    public function getPaginatedSchema(string $schema)
    {
        return [
            "type" => "object",
            "properties" => [
                "page" => [
                    "type" => "integer"
                ],
                "per_page" => [
                    "type" => "integer"
                ],
                "total" => [
                    "type" => "integer"
                ],
                "data" => $this->getSchemaArray($schema)
            ]
        ];
    }


    /**
     * Preprocess nullable.
     * @param $schema
     *
     * @return array
     */
    public function preProcess($schema): array
    {
        if ($schema["type"] === "object" && !empty($schema["properties"])) {
            /**
             * Handle OpenAPI nullable
             *
             * @link https://github.com/justinrainbow/json-schema/issues/551
             */
            foreach ($schema["properties"] as $property => $spec) {
                if (isset($spec["type"]) && isset($spec["nullable"]) && $spec["nullable"] === true) {
                    unset($schema["properties"][$property]["type"]);
                    $currentTypeDefinition = ["type" => $spec["type"]];

                    if (isset($spec["format"])) {
                        $currentTypeDefinition["format"] = $spec["format"];
                        unset($schema["properties"][$property]["format"]);
                    }

                    $schema["properties"][$property]["anyOf"] = [
                        $currentTypeDefinition,
                        ["type" => "null"]
                    ];
                }
            }
        }

        return $schema;
    }
}
