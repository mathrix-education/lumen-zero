<?php

namespace Mathrix\Lumen\Services\OpenAPI;

use Illuminate\Support\Arr;

/**
 * Class NullablePreProcessor.
 * Handle OpenAPI nullable property, since it diverges from JSON Schema specification.
 * @link https://github.com/justinrainbow/json-schema/issues/551
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class NullablePreProcessor implements PreProcessor
{
    /**
     * Transform the schema.
     * @param array $schema The schema before the transformation.
     * @return array The schema after the transformation.
     */
    public function transform(array $schema): array
    {
        return $this->analyse($schema);
    }


    /**
     * Recursively analyse a schema and patch nullable properties.
     * @param array $schema The schema to analyse.
     * @return array The mutated schema.
     */
    public function analyse(array $schema)
    {
        if (!$this->isObject($schema)) {
            return $schema;
        }

        foreach ($schema["properties"] as $key => $subSchema) {
            if ($this->isObject($subSchema)) {
                $schema["properties"][$key] = $this->analyse($subSchema);
            } elseif ($this->isNullable($subSchema)) {
                $schema["properties"][$key] = $this->handleNullable($subSchema);
            }
        }

        return $schema;
    }


    /**
     * Check if a schema is an object and thus need analyse.
     * @param array $schema The schema.
     * @return bool
     */
    private function isObject(array $schema): bool
    {
        return $schema["type"] === "object" && !empty($schema["properties"]);
    }


    /**
     * Check if a schema is nullable.
     * @param array $schema The schema.
     * @return bool
     */
    private function isNullable(array $schema): bool
    {
        return isset($schema["nullable"]) && $schema["nullable"] === true;
    }


    /**
     * Handle the nullable property. We assume here that the schema has a nullable property.
     * @param array $schema
     * @return array The mutated schema.
     */
    private function handleNullable(array $schema): array
    {
        $toMigrate = ["type", "format", "enum"];

        /** @var array $actual The sanitized schema (without nullable fields) */
        $actual = Arr::except($schema, $toMigrate);
        /** @var array $declaration We save the interesting data here */
        $declaration = Arr::only($schema, $toMigrate);
        /** @var array $anyOf The new anyOf field which handle the multi-type. */
        $anyOf = [
            ["type" => "null"],
            $declaration
        ];

        return array_merge($actual, ["anyOf" => $anyOf]);
    }
}
