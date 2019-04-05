<?php

namespace Mathrix\Lumen\Services\OpenAPI;

use stdClass;

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
     * @param stdClass|null $schema The schema before the transformation.
     * @return stdClass|null The schema after the transformation.
     */
    public function transform(?stdClass $schema): ?stdClass;
}
