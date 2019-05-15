<?php

namespace Mathrix\Lumen\Zero\Tests\OpenAPI;

use Illuminate\Support\Arr;
use stdClass;

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
     * @param stdClass|null $schema The schema before the transformation.
     * @return stdClass|null The schema after the transformation.
     */
    public function transform(?stdClass $schema): ?stdClass
    {
        $array = json_decode(json_encode($schema), true);
        $transformed = $this->analyse($array);

        return json_decode(json_encode($transformed));
    }


    /**
     * Recursively analyse a schema and patch nullable properties.
     * @param array $schema The schema to analyse.
     * @return array The mutated schema.
     */
    public function analyse(array $schema)
    {
        if (!is_array($schema)) {
            return $schema;
        }

        foreach ($schema as $key => $subSchema) {
            if (is_array($subSchema)) {
                if ($this->isNullable($subSchema)) {
                    $schema[$key] = $this->handleNullable($subSchema);
                } else {
                    $schema[$key] = $this->analyse($subSchema);
                }
            }
        }

        return $schema;
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
