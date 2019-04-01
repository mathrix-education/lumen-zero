<?php

namespace Mathrix\Lumen\Services\OpenAPI;

/**
 * Interface BasePreProcessor.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
interface PreProcessor
{
    /**
     * Transform the schema.
     * @param array $schema The schema before the transformation.
     * @return array The schema after the transformation.
     */
    public function transform(array $schema): array;
}
